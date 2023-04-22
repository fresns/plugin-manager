<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Providers;

use Fresns\PluginManager\Plugin;
use Fresns\PluginManager\Support\Json;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->autoload();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/plugins.php', 'plugins');
        $this->publishes([
            __DIR__.'/../../config/plugins.php' => config_path('plugins.php'),
        ], 'laravel-plugin-config');

        $this->addMergePluginConfig();

        $this->registerCommands([
            __DIR__.'/../Commands/*',
        ]);
    }

    public function registerCommands($paths)
    {
        $allCommand = [];

        foreach ($paths as $path) {
            $commandPaths = glob($path);

            foreach ($commandPaths as $command) {
                $commandPath = realpath($command);
                if (! is_file($commandPath)) {
                    continue;
                }

                $commandClass = 'Fresns\\PluginManager\\Commands\\'.pathinfo($commandPath, PATHINFO_FILENAME);

                if (class_exists($commandClass)) {
                    $allCommand[] = $commandClass;
                }
            }
        }

        $this->commands($allCommand);
    }

    protected function autoload()
    {
        $this->addFiles();

        $plugin = new Plugin();

        collect($plugin->all())->map(function ($pluginName) {
            try {
                $plugin = new Plugin($pluginName);

                if ($plugin->isAvailablePlugin() && $plugin->isActivate()) {
                    $plugin->registerFiles();
                    $plugin->registerProviders();
                    $plugin->registerAliases();
                }
            } catch (\Throwable $e) {
                info($message = sprintf('Plugin namespace failed to load UniKey: %s, reason: %s, file: %s, line: %s',
                    $pluginName,
                    $e->getMessage(),
                    str_replace(base_path().'/', '', $e->getFile()),
                    $e->getLine(),
                ));
            }
        });
    }

    protected function addFiles()
    {
        $files = $this->app['config']->get('plugins.autoload_files');

        foreach ($files as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }

    protected function addMergePluginConfig()
    {
        $composerPath = base_path('composer.json');
        $composer = Json::make($composerPath)->get();
        if (! $composer) {
            info('Failed to get base_path("composer.json") content');

            return;
        }

        $userMergePluginConfig = Arr::get($composer, 'extra.merge-plugin', []);

        $defaultMergePlugin = config('plugins.merge_plugin_config', []);
        if (empty($defaultMergePlugin)) {
            $config = require config_path('plugins.php');
            $defaultMergePlugin = $config['merge_plugin_config'];
        }

        if (empty($defaultMergePlugin)) {
            info('Failed to get plugins.merge_plugin_config, please publish the plugins configuration file');

            return;
        }

        $mergePluginConfig = array_merge($defaultMergePlugin, $userMergePluginConfig);

        // merge include
        $diffInclude = array_diff($defaultMergePlugin['include'] ?? [], $userMergePluginConfig['include'] ?? []);
        $mergePluginConfigInclude = array_merge($diffInclude, $userMergePluginConfig['include'] ?? []);

        $mergePluginConfig['include'] = $mergePluginConfigInclude;

        // compatible with Windows system.
        if (str_contains(strtolower(PHP_OS_FAMILY), 'win')) {
            $includes = $mergePluginConfig['include'] ?? [];

            $newIncludes = [];
            foreach ($includes as $includeComposerJson) {
                $newIncludeComposerJson = str_replace('/', '\\\\', $includeComposerJson);
                $newIncludes[] = $newIncludeComposerJson;
            }

            $mergePluginConfig['include'] = $newIncludes;
        }

        Arr::set($composer, 'extra.merge-plugin', $mergePluginConfig);

        try {
            $content = Json::make()->encode($composer);
            $content .= "\n";

            $fp = fopen($composerPath, 'r+');
            if (flock($fp, LOCK_EX | LOCK_NB)) {
                fwrite($fp, $content);
                flock($fp, LOCK_UN);
            }
            fclose($fp);
        } catch (\Throwable $e) {
            $message = str_replace(['file_put_contents('.base_path().'/', ')'], '', $e->getMessage());
            throw new \RuntimeException('cannot set merge-plugin to '.$message);
        }
    }
}
