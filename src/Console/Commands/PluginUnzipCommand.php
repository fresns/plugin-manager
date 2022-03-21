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
    protected $description = 'Unzip the plugin files into the /plugins/ directory, the final directory will be /plugins/{unikey}/.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->filesystem = app('files');
        $this->pluginRepository = app('plugins.repository');
        $this->localPath = $this->argument('path');

        if ($this->filesystem->isDirectory($this->localPath)) {
            $jsonFile = 'plugin.json';
            if ($this->option('type') === PluginConstant::PLUGIN_TYPE_THEME) {
                $jsonFile = 'theme.json';
            }

            if (!$this->filesystem->exists("{$this->localPath}/{$jsonFile}")) {
                throw new LocalPathNotFoundException("Local Path [{$this->localPath}/{$jsonFile}] does not exist!");
            }

            $pluginName = Json::make("{$this->localPath}/{$jsonFile}")->get('unikey');

            if ($this->pluginRepository->has($pluginName)) {
                throw new PluginAlreadyExistException("Plugin [{$pluginName}] already exists!");
            }

            $buildPluginPath = $this->pluginRepository->getPluginPath($pluginName);

            if (!$this->filesystem->isDirectory($buildPluginPath)) {
                $this->filesystem->makeDirectory($buildPluginPath, 0775, true);
            }

            $this->filesystem->copyDirectory(
                $this->localPath,
                $buildPluginPath
            );
        } elseif ($this->filesystem->isFile($this->localPath) && $this->filesystem->extension($this->localPath) === 'zip') {
            $pluginName = (new DecompressPlugin($this->localPath, $this->option('type')))->handle();
        } else {
            // The path passed is not a zip archive, nor is it the directory of the plugin unikey name
            throw new \RuntimeException("Local Path [{$this->localPath}] does not support to unzip!");
        }

        $this->info("Plugin $pluginName unzip success.");

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

    protected function getOptions(): array
    {
        return [
            ['type', null, InputOption::VALUE_OPTIONAL, 'This plugin type.', PluginConstant::PLUGIN_TYPE_EXTENSION],
        ];
    }
}
