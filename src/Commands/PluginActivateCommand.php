<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Illuminate\Console\Command;

class PluginActivateCommand extends Command
{
    use Traits\WorkPluginFskeyTrait;

    protected $signature = 'plugin:activate {fskey?}';

    protected $description = 'Activate Plugin';

    public function handle()
    {
        $pluginFskey = $this->getPluginFskey();

        if ($pluginFskey) {
            $this->activate($pluginFskey);
        }
        // Activate all plugins
        else {
            $this->activateAll();
        }

        $this->info('Plugin activate successfully');

        return Command::SUCCESS;
    }

    public function activateAll()
    {
        $plugin = new Plugin();

        collect($plugin->all())->map(function ($pluginFskey) {
            $this->activate($pluginFskey);
        });
    }

    public function activate(?string $pluginFskey = null)
    {
        $plugin = new Plugin($pluginFskey);

        $fskey = $plugin->getStudlyName();

        event('plugin:activating', [[
            'fskey' => $fskey,
        ]]);

        if ($result = $plugin->activate()) {
            $this->info(sprintf('Plugin %s activate successfully', $pluginFskey));
        } else {
            $this->error(sprintf('Plugin %s activate failure', $pluginFskey));
        }

        event('plugin:activated', [[
            'fskey' => $fskey,
        ]]);

        return $result;
    }
}
