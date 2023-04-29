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
    use Traits\WorkPluginUnikeyTrait;

    protected $signature = 'plugin:deactivate {unikey?}';

    protected $description = 'Deactivate plugin';

    public function handle()
    {
        $pluginUnikey = $this->getPluginUnikey();

        if ($pluginUnikey) {
            $this->deactivate($pluginUnikey);
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

        collect($plugin->all())->map(function ($pluginUnikey) {
            $this->deactivate($pluginUnikey);
        });
    }

    public function deactivate(?string $pluginUnikey = null)
    {
        $plugin = new Plugin($pluginUnikey);
        $unikey = $plugin->getStudlyName();

        event('plugin:deactivating', [[
            'unikey' => $unikey,
        ]]);

        if ($result = $plugin->deactivate()) {
            $this->info(sprintf('Plugin %s deactivate successfully', $pluginUnikey));
        } else {
            $this->error(sprintf('Plugin %s deactivate failure', $pluginUnikey));
        }

        event('plugin:deactivated', [[
            'unikey' => $unikey,
        ]]);

        return $result;
    }
}
