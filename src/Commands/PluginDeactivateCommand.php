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
            $status = $this->deactivate($pluginFskey);
        } else {
            // Deactivate all plugins
            $status = $this->deactivateAll();
        }

        if (! $status) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    public function deactivateAll()
    {
        $plugin = new Plugin();

        $status = true;

        collect($plugin->all())->each(function ($pluginFskey) use (&$status) {
            if (! $this->deactivate($pluginFskey)) {
                $status = false;
            }
        });

        return $status;
    }

    public function deactivate(?string $pluginFskey = null)
    {
        $plugin = new Plugin($pluginFskey);
        $fskey = $plugin->getStudlyName();

        event('plugin:deactivating', [[
            'fskey' => $fskey,
        ]]);

        if ($plugin->deactivate()) {
            $this->info(sprintf('Plugin %s deactivated successfully', $pluginFskey));

            event('plugin:deactivated', [[
                'fskey' => $fskey,
            ]]);

            return true;
        }

        $this->error(sprintf('Plugin %s deactivated failed', $pluginFskey));

        return false;
    }
}
