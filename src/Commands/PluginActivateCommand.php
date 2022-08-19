<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Illuminate\Console\Command;

class PluginActivateCommand extends Command
{
    protected $signature = 'plugin:activate {name?}';

    protected $description = 'Activate Plugin';

    public function handle()
    {
        if ($pluginName = $this->argument('name')) {
            $this->activate($pluginName);
        }
        // Activate all plugins
        else {
            $this->activateAll();
        }

        $this->info('Plugin activate successfully');

        return 0;
    }

    public function activateAll()
    {
        $plugin = new Plugin();

        collect($plugin->all())->map(function ($pluginName) {
            $this->activate($pluginName);
        });
    }

    public function activate(?string $pluginName = null)
    {
        $plugin = new Plugin($pluginName);

        $unikey = $plugin->getStudlyName();
        $type = $plugin->getType();

        event('plugin:activating', [
            'unikey' => $unikey,
            'type' => $type,
        ]);

        if ($result = $plugin->activate()) {
            $this->info(sprintf('Plugin %s activate successfully', $pluginName));
        } else {
            $this->info(sprintf('Plugin %s activate failure', $pluginName));
        }

        event('plugin:activated', [
            'unikey' => $unikey,
            'type' => $type,
        ]);

        return $result;
    }
}
