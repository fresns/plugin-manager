<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Illuminate\Console\Command;

class PluginDeactivateCommand extends Command
{
    use Traits\WorkPluginNameTrait;

    protected $signature = 'plugin:deactivate {name?}';

    protected $description = 'Deactivate plugin';

    public function handle()
    {
        $pluginName = $this->getPluginName();

        if ($pluginName) {
            $this->deactivate($pluginName);
        }
        // Deactivate all plugins
        else {
            $this->deactivateAll();
        }

        $this->info('Plugin deactivate successfully');

        return Command::SUCCESS;
    }

    public function deactivateAll()
    {
        $plugin = new Plugin();

        collect($plugin->all())->map(function ($pluginName) {
            $this->deactivate($pluginName);
        });
    }

    public function deactivate(?string $pluginName = null)
    {
        $plugin = new Plugin($pluginName);
        $unikey = $plugin->getStudlyName();

        event('plugin:deactivating', [[
            'unikey' => $unikey,
        ]]);

        if ($result = $plugin->deactivate()) {
            $this->info(sprintf('Plugin %s deactivate successfully', $pluginName));
        } else {
            $this->info(sprintf('Plugin %s deactivate successfully', $pluginName));
        }

        event('plugin:deactivated', [[
            'unikey' => $unikey,
        ]]);

        return $result;
    }
}
