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
    use Traits\WorkPluginFskeyTrait;

    protected $signature = 'plugin:deactivate {fskey?}';

    protected $description = 'Deactivate plugin';

    public function handle()
    {
        $pluginFskey = $this->getPluginFskey();

        if ($pluginFskey) {
            $this->deactivate($pluginFskey);
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

        collect($plugin->all())->map(function ($pluginFskey) {
            $this->deactivate($pluginFskey);
        });
    }

    public function deactivate(?string $pluginFskey = null)
    {
        $plugin = new Plugin($pluginFskey);
        $fskey = $plugin->getStudlyName();

        event('plugin:deactivating', [[
            'fskey' => $fskey,
        ]]);

        if ($result = $plugin->deactivate()) {
            $this->info(sprintf('Plugin %s deactivate successfully', $pluginFskey));
        } else {
            $this->error(sprintf('Plugin %s deactivate failure', $pluginFskey));
        }

        event('plugin:deactivated', [[
            'fskey' => $fskey,
        ]]);

        return $result;
    }
}
