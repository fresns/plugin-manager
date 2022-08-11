<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Illuminate\Console\Command;
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
            $this->call('plugin:unzip', [
                'path' => $path,
            ]);

            $unikey = Cache::pull('install:plugin_unikey');
            if (! $unikey) {
                info('Failed to unzip, couldn\'t get the plugin unikey');

                return 0;
            }
            $plugin = new Plugin($unikey);
            $plugin->manualAddNamespace();

            $this->call('plugin:publish', [
                'name' => $plugin->getStudlyName(),
            ]);

            // Triggers top-level computation of composer.json hash values and installation of extension packages
            $isOk = @exec('composer update');
            if ($isOk === false) {
                throw new \RuntimeException('Failed to install packages');
            }

            $this->call('plugin:deactivate', [
                'name' => $plugin->getStudlyName(),
            ]);

            $this->call('plugin:migrate', [
                'name' => $plugin->getStudlyName(),
            ]);

            if ($this->option('seed')) {
                $this->call('plugin:seed', [
                    'name' => $plugin->getStudlyName(),
                ]);
            }

            $this->info("Installed: {$plugin->getStudlyName()}");
        } catch (\Throwable $e) {
            $this->error("Install fail: {$e->getMessage()}");
        }

        return 0;
    }
}
