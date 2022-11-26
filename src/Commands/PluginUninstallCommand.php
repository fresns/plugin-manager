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
use Illuminate\Support\Facades\File;

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

            $composerJson = Json::make($plugin->getComposerJsonPath())->get();
            $require = Arr::get($composerJson, 'require', []);
            $requireDev = Arr::get($composerJson, 'require-dev', []);

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
            if (count($require) || count($requireDev)) {
                $exitCode = $this->call('plugin:composer-update');
                if ($exitCode) {
                    $this->error('Failed to update plugin dependency');

                    return Command::FAILURE;
                }
            }

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
