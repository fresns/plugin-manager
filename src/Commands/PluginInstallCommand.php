<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Fresns\PluginManager\Support\Json;
use Fresns\PluginManager\Support\Process;
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
            if (! str_contains($path, config('plugins.paths.plugins'))) {
                $this->call('plugin:unzip', [
                    'path' => $path,
                ]);

                $unikey = Cache::pull('install:plugin_unikey');
            } else {
                $unikey = dirname($path);
            }

            if (! $unikey) {
                info('Failed to unzip, couldn\'t get the plugin unikey');

                return 0;
            }

            $plugin = new Plugin($unikey);
            if (! $plugin->isValidPlugin()) {
                $this->error('plugin is not an avaliable plugin');

                return 0;
            }

            $plugin->manualAddNamespace();

            event('plugin:installing', [[
                'unikey' => $unikey,
            ]]);

            $this->call('plugin:publish', [
                'name' => $unikey,
            ]);

            $composerJson = Json::make($plugin->getComposerJsonPath())->get();
            $require = Arr::get($composerJson, 'require', []);
            $requireDev = Arr::get($composerJson, 'require-dev', []);

            // Triggers top-level computation of composer.json hash values and installation of extension packages
            // @see https://getcomposer.org/doc/03-cli.md#process-exit-codes
            if (count($require) || count($requireDev)) {
                $process = Process::run('composer update', $this->output);
                if (! $process->isSuccessful()) {
                    $this->error('Failed to install packages, calc composer.json hash value fail');

                    return 0;
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

            event('plugin:installed', [[
                'unikey' => $unikey,
            ]]);

            $this->info("Installed: {$unikey}");
        } catch (\Throwable $e) {
            $this->error("Install fail: {$e->getMessage()}");
        }

        return 0;
    }
}
