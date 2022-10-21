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

                return -1;
            }

            $plugin = new Plugin($unikey);
            if (! $plugin->isValidPlugin()) {
                $this->error('plugin is invalid, unikey: '.$unikey);

                return -1;
            }

            $plugin->manualAddNamespace();

            event('plugin:installing', [[
                'unikey' => $unikey,
            ]]);

            $exitCode = $this->call('plugin:publish', [
                'name' => $unikey,
            ]);

            if ($exitCode != 0) {
                return $exitCode;
            }

            $composerJson = Json::make($plugin->getComposerJsonPath())->get();
            $require = Arr::get($composerJson, 'require', []);
            $requireDev = Arr::get($composerJson, 'require-dev', []);

            // Triggers top-level computation of composer.json hash values and installation of extension packages
            // @see https://getcomposer.org/doc/03-cli.md#process-exit-codes
            if (count($require) || count($requireDev)) {
                $process = Process::run('composer update', $this->output);
                if (! $process->isSuccessful()) {
                    $this->error('Failed to install packages, calc composer.json hash value fail');

                    return -1;
                }
            }

            $exitCode = $this->call('plugin:deactivate', [
                'name' => $unikey,
            ]);

            if ($exitCode != 0) {
                return $exitCode;
            }

            $exitCode = $this->call('plugin:migrate', [
                'name' => $unikey,
                '--force' => true,
            ]);

            if ($exitCode != 0) {
                return $exitCode;
            }

            if ($this->option('seed')) {
                $exitCode = $this->call('plugin:seed', [
                    'name' => $unikey,
                    '--force' => true,
                ]);

                if ($exitCode != 0) {
                    return $exitCode;
                }
            }

            $plugin->install();

            event('plugin:installed', [[
                'unikey' => $unikey,
            ]]);

            $this->info("Installed: {$unikey}");
        } catch (\Throwable $e) {
            $this->error("Install fail: {$e->getMessage()}");

            return -1;
        }

        return 0;
    }
}
