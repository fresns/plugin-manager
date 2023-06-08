<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Fresns\PluginManager\Support\Json;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class PluginInstallCommand extends Command
{
    protected $signature = 'plugin:install {path}
        {--seed}
        {--is_dir}
        ';

    protected $description = 'Install the plugin from the specified path';

    public function handle()
    {
        try {
            $path = $this->argument('path');

            if ($this->option('is_dir')) {
                $pluginDirectory = $path;

                if (strpos($pluginDirectory, '/') == false) {
                    $pluginDirectory = "extensions/plugins/{$pluginDirectory}";
                }

                if (str_starts_with($pluginDirectory, '/')) {
                    $pluginDirectory = realpath($pluginDirectory);
                } else {
                    $pluginDirectory = realpath(base_path($pluginDirectory));
                }

                $path = $pluginDirectory;
            }

            if (! $path || ! file_exists($path)) {
                $this->error('Failed to unzip, couldn\'t find the plugin path');

                return Command::FAILURE;
            }

            $extensionPath = str_replace(base_path().'/', '', config('plugins.paths.plugins'));
            if (! str_contains($path, $extensionPath)) {
                $exitCode = $this->call('plugin:unzip', [
                    'path' => $path,
                ]);

                if ($exitCode != 0) {
                    return $exitCode;
                }

                $fskey = Cache::pull('install:plugin_fskey');
            } else {
                $fskey = basename($path);
            }

            if (! $fskey) {
                info('Failed to unzip, couldn\'t get the plugin fskey');

                return Command::FAILURE;
            }

            $plugin = new Plugin($fskey);
            if (! $plugin->isValidPlugin()) {
                $this->error('plugin is invalid');

                return Command::FAILURE;
            }

            $plugin->manualAddNamespace();

            event('plugin:installing', [[
                'fskey' => $fskey,
            ]]);

            $composerJson = Json::make($plugin->getComposerJsonPath())->get();
            $require = Arr::get($composerJson, 'require', []);
            $requireDev = Arr::get($composerJson, 'require-dev', []);

            // Triggers top-level computation of composer.json hash values and installation of extension packages
            // @see https://getcomposer.org/doc/03-cli.md#process-exit-codes
            if (count($require) || count($requireDev)) {
                $exitCode = $this->call('plugin:composer-update');
                if ($exitCode) {
                    $this->error('Failed to update plugin dependency');

                    return Command::FAILURE;
                }
            }

            $this->call('plugin:deactivate', [
                'fskey' => $fskey,
            ]);

            $this->call('plugin:migrate', [
                'fskey' => $fskey,
            ]);

            if ($this->option('seed')) {
                $this->call('plugin:seed', [
                    'fskey' => $fskey,
                ]);
            }

            $plugin->install();

            $this->call('plugin:publish', [
                'fskey' => $fskey,
            ]);

            event('plugin:installed', [[
                'fskey' => $fskey,
            ]]);

            $this->info("Installed: {$fskey}");
        } catch (\Throwable $e) {
            info("Install fail: {$e->getMessage()}");
            $this->error("Install fail: {$e->getMessage()}");

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
