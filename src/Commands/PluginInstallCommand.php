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

            $type = $plugin->getType();

            event('plugin:installing', [
                'unikey' => $unikey,
                'type' => $type,
            ]);

            $this->call('plugin:publish', [
                'name' => $unikey,
            ]);

            // Triggers top-level computation of composer.json hash values and installation of extension packages
            $isOk = @exec('composer update');
            if ($isOk === false) {
                throw new \RuntimeException('Failed to install packages');
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

            event('plugin:installed', [
                'unikey' => $unikey,
                'type' => $type,
            ]);

            $this->info("Installed: {$unikey}");
        } catch (\Throwable $e) {
            $this->error("Install fail: {$e->getMessage()}");
        }

        return 0;
    }
}
