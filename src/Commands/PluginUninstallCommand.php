<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PluginUninstallCommand extends Command
{
    protected $signature = 'plugin:uninstall {name}';

    protected $description = 'Install the plugin from the specified path';

    public function handle()
    {
        try {
            $unikey = $this->argument('name');
            $this->call('plugin:deactivate', [
                'name' => $unikey,
            ]);

            $this->call('plugin:migrate-rollback', [
                'name' => $unikey,
            ]);

            $this->call('plugin:unpublish', [
                'name' => $unikey,
            ]);

            $plugin = new Plugin($unikey);
            File::delete($plugin->getCachedServicesPath());
            File::deleteDirectory($plugin->getPluginPath());

            // Triggers top-level computation of composer.json hash values and installation of extension packages
            @exec('composer update');

            $this->info("Uninstalled: {$unikey}");
        } catch (\Throwable $e) {
            $this->error("Uninstall fail: {$e->getMessage()}");
        }

        return 0;
    }
}
