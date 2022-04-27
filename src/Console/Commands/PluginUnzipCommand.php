<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Exceptions\LocalPathNotFoundException;
use Fresns\PluginManager\Exceptions\PluginAlreadyExistException;
use Fresns\PluginManager\Support\DecompressPlugin;
use Fresns\PluginManager\Support\Json;
use Fresns\PluginManager\Support\PluginConstant;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PluginUnzipCommand extends Command
{
    const CACHE_INSTALL_PLUGIN_NAME = 'install-plugin-name';
    
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:unzip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unzip the plugin files into the /plugins/ directory.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->filesystem = app('files');
        $this->pluginRepository = app('plugins.repository');
        $this->localPath = rtrim($this->argument('path'), '/');

        if ($this->filesystem->isDirectory($this->localPath)) {
            $jsonFileName = match ($this->option('type')) {
                PluginConstant::PLUGIN_TYPE_THEME => 'theme.json',
                default => 'plugin.json',
            };

            $jsonFilePath = "{$this->localPath}/{$jsonFileName}";

            $files = $this->filesystem->glob("{$this->localPath}/{$jsonFileName}");
            if (count($files) === 0) {
                // Deeper level scanning
                $files = $this->filesystem->glob("{$this->localPath}/**/{$jsonFileName}");

                if (count($files) === 0) {
                    throw new LocalPathNotFoundException("Local Path [{$this->localPath}/{$jsonFileName}] does not exist!");
                }
            }

            $jsonFilePath = head($files);

            $pluginName = Json::make($jsonFilePath)->get('unikey');

            static::backupExistsPlugin($pluginName);

            if ($this->pluginRepository->has($pluginName)) {
                throw new PluginAlreadyExistException("Plugin [{$pluginName}] already exists!");
            }

            $buildPluginPath = base_path("plugins/$pluginName");
            if ($this->option('type') === PluginConstant::PLUGIN_TYPE_THEME) {
                $buildPluginPath = resource_path("themes/$pluginName");
            }

            if (! $this->filesystem->isDirectory($buildPluginPath)) {
                $this->filesystem->makeDirectory($buildPluginPath, 0775, true);
            }

            $this->filesystem->copyDirectory(
                pathinfo($jsonFilePath, PATHINFO_DIRNAME),
                $buildPluginPath,
                true,
            );
        } elseif ($this->filesystem->isFile($this->localPath) && $this->filesystem->extension($this->localPath) === 'zip') {
            $pluginName = (new DecompressPlugin($this->localPath, $this->option('type')))->handle();
        } else {
            // The path passed is not a zip archive, nor is it the directory of the plugin unikey name
            throw new \RuntimeException("Local Path [{$this->localPath}] does not support to unzip!");
        }

        // Put the name of the plugin being installed into the cache for 5 minutes validity.
        // Make sure that the name of the plugin is known elsewhere during installation.
        cache([static::CACHE_INSTALL_PLUGIN_NAME => $pluginName], now()->addMinutes(5));

        $this->info("Plugin $pluginName unzip success.");

        return 0;
    }

    public static function backupExistsPlugin(string $pluginName)
    {
        $path = base_path("plugins/{$pluginName}");
        $targetPath = storage_path("plugins/{$pluginName}");

        if (!is_dir($path)) {
            return;
        }

        static::ensureBackupDirExists();

        $dirs = app('files')->glob($targetPath.'*');

        $currentBackupCount = count($dirs);

        $targetPath .= $currentBackupCount+1;

        app('files')->moveDirectory($path, $targetPath);
    }

    public static function ensureBackupDirExists()
    {
        $path = storage_path('plugins');

        if (!app('files')->isDirectory($path)) {
            app('files')->makeDirectory($path, 0755, true);
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['path', InputArgument::REQUIRED, 'Plugin zip location.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['type', null, InputOption::VALUE_OPTIONAL, 'This plugin type.', PluginConstant::PLUGIN_TYPE_EXTENSION],
        ];
    }
}
