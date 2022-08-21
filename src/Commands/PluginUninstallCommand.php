<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Illuminate\Console\Command;
use Fresns\PluginManager\Plugin;
use Illuminate\Support\Facades\File;
use Fresns\PluginManager\Support\Process;

class PluginUninstallCommand extends Command
{
    protected $signature = 'plugin:uninstall {name}
        {--cleardata : Trigger clear plugin data}';

    protected $description = 'Install the plugin from the specified path';

    public function handle()
    {
        try {
            $unikey = $this->argument('name');
            $plugin = new Plugin($unikey);

            event('plugin:uninstalling', [[
                'unikey' => $unikey,
            ]]);

            $this->call('plugin:deactivate', [
                'name' => $unikey,
            ]);

            if ($this->option('cleardata')) {
                event('plugins.cleandata', [[
                    'unikey' => $unikey,
                ]]);

                $this->call('plugin:migrate-rollback', [
                    'name' => $unikey,
                ]);
            }

            $this->call('plugin:unpublish', [
                'name' => $unikey,
            ]);

            File::delete($plugin->getCachedServicesPath());
            File::deleteDirectory($plugin->getPluginPath());

            // Triggers top-level computation of composer.json hash values and installation of extension packages
            Process::run('composer update', $this->output);


            $plugin->uninstall();

            event('plugin:uninstalled', [[
                'unikey' => $unikey,
            ]]);

            $this->info("Uninstalled: {$unikey}");
        } catch (\Throwable $e) {
            $this->error("Uninstall fail: {$e->getMessage()}");
        }

        return 0;
    }
}
