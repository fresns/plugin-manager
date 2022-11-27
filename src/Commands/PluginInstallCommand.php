<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
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
        ';

    protected $description = 'Install the plugin from the specified path';

    public function handle()
    {
        try {
            $path = $this->argument('path');
            $extensionPath = str_replace(base_path().'/', '', config('plugins.paths.plugins'));
            if (! str_contains($path, $extensionPath)) {
                $exitCode = $this->call('plugin:unzip', [
                    'path' => $path,
                ]);

                if ($exitCode != 0) {
                    return $exitCode;
                }

                $unikey = Cache::pull('install:plugin_unikey');
            } else {
                $unikey = basename($path);
            }

            if (! $unikey) {
                info('Failed to unzip, couldn\'t get the plugin unikey');

                return Command::FAILURE;
            }

            $plugin = new Plugin($unikey);
            if (! $plugin->isValidPlugin()) {
                $this->error('plugin is invalid');

                return Command::FAILURE;
            }

            $plugin->manualAddNamespace();

            event('plugin:installing', [[
                'unikey' => $unikey,
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
                'name' => $unikey,
            ]);

            $this->call('plugin:migrate', [
                'name' => $unikey,
            ]);

            if ($this->option('seed')) {
                $this->call('plugin:seed', [
                    'name' => $unikey,
                ]);
            }

            $plugin->install();

            $this->call('plugin:publish', [
                'name' => $unikey,
            ]);

            event('plugin:installed', [[
                'unikey' => $unikey,
            ]]);

            $this->info("Installed: {$unikey}");
        } catch (\Throwable $e) {
            $this->error("Install fail: {$e->getMessage()}");
        }

        return Command::SUCCESS;
    }
}
