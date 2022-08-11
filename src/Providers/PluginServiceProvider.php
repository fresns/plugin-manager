<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Providers;

use Illuminate\Support\ServiceProvider;
use Fresns\PluginManager\Plugin;
use Fresns\PluginManager\Support\Json;
use Illuminate\Support\Arr;

class PluginServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->autoload();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/plugins.php', 'plugins');
        $this->publishes([
            __DIR__ . '/../../config/plugins.php' => config_path('plugins.php'),
        ], 'laravel-plugin-config');

        $this->addMergePluginConfig();

        $this->registerCommands([
            __DIR__ . '/../Commands/*',
        ]);
    }

    public function registerCommands($paths)
    {
        $allCommand = [];

        foreach ($paths as $path) {
            $commandPaths = glob($path);

            foreach ($commandPaths as $command) {
                $commandPath = realpath($command);
                if (!is_file($commandPath)) {
                    continue;
                }

                $commandClass = "Fresns\\PluginManager\\Commands\\" . pathinfo($commandPath, PATHINFO_FILENAME);

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
            $plugin = new Plugin($pluginName);

            $plugin->registerProviders();
            $plugin->registerAliases();
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

        $userMergePluginConfig = Arr::get($composer, 'extra.merge-plugin', []);

        $defaultMergePlugin = config('plugins.merge_plugin_config');
        $mergePluginConfig = array_merge($defaultMergePlugin, $userMergePluginConfig);

        Arr::set($composer, 'extra.merge-plugin', $mergePluginConfig);

        try {
            $content = Json::make()->encode($composer);

            file_put_contents($composerPath, $content);
        } catch (\Throwable $e) {
            $message = str_replace(['file_put_contents(' . base_path() . '/', ')'], '', $e->getMessage());
            throw new \RuntimeException('cannot set merge-plugin to ' . $message);
        }
    }
}
