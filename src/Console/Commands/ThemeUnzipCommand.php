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

class ThemeUnzipCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:unzip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unzip the plugin files into the /resources/themes/ directory, the final directory will be /resources/themes/{unikey}/.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->filesystem = app('files');
        $this->pluginRepository = app('plugins.repository');
        $this->localPath = $this->argument('path');

        if ($this->filesystem->isDirectory($this->localPath)) {
            if (! $this->filesystem->exists("{$this->localPath}/theme.json")) {
                throw new LocalPathNotFoundException("Local Path [{$this->localPath}] does not exist!");
            }

            $pluginName = Json::make("{$this->localPath}/theme.json")->get('unikey');

            if ($this->pluginRepository->has($pluginName)) {
                throw new PluginAlreadyExistException("Plugin [{$pluginName}] already exists!");
            }

            $buildPluginPath = $this->getBuildThemePath($pluginName);

            if (! $this->filesystem->isDirectory($buildPluginPath)) {
                $this->filesystem->makeDirectory($buildPluginPath, 0775, true);
            }

            $this->filesystem->copyDirectory(
                $this->localPath,
                $buildPluginPath
            );
        } elseif ($this->filesystem->isFile($this->localPath) && $this->filesystem->extension($this->localPath) === 'zip') {
            $pluginName = (new DecompressPlugin($this->localPath, PluginConstant::PLUGIN_TYPE_THEME))->handle();
        } else {
            // The path passed is not a zip archive, nor is it the directory of the plugin unikey name
            throw new \RuntimeException("Local Path [{$this->localPath}] does not support to unzip!");
        }

        $this->info("Theme $pluginName unzip to resources/themes/{$pluginName}/ success.");

        return 0;
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
}
