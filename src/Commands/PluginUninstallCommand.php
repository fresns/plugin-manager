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
use Illuminate\Support\Facades\File;

class PluginUninstallCommand extends Command
{
    use Traits\WorkPluginFskeyTrait;

    protected $signature = 'plugin:uninstall {fskey?}
        {--cleardata=}';

    protected $description = 'Install the plugin from the specified path';

    public function handle()
    {
        try {
            $pluginFskey = $this->getPluginFskey();
            $plugin = new Plugin($pluginFskey);

            if ($this->validatePluginRootPath($plugin)) {
                $this->error('Failed to operate plugins root path');

                return Command::FAILURE;
            }

            $composerJson = Json::make($plugin->getComposerJsonPath())->get();
            $require = Arr::get($composerJson, 'require', []);
            $requireDev = Arr::get($composerJson, 'require-dev', []);

            event('plugin:uninstalling', [[
                'fskey' => $pluginFskey,
            ]]);

            $this->call('plugin:deactivate', [
                'fskey' => $pluginFskey,
            ]);

            if ($this->option('cleardata')) {
                $this->call('plugin:migrate-rollback', [
                    'fskey' => $pluginFskey,
                ]);

                $this->info("Clear Data: {$pluginFskey}");
            }

            $this->call('plugin:unpublish', [
                'fskey' => $pluginFskey,
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
                'fskey' => $pluginFskey,
            ]]);

            $this->info("Uninstalled: {$pluginFskey}");
        } catch (\Throwable $e) {
            info("Uninstall fail: {$e->getMessage()}");
            $this->error("Uninstall fail: {$e->getMessage()}");

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
