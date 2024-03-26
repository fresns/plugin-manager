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
            $status = $this->activate($pluginFskey);
        } else {
            // Activate all plugins
            $status = $this->activateAll();
        }

        if (! $status) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    public function activateAll()
    {
        $plugin = new Plugin();

        $status = true;

        collect($plugin->all())->each(function ($pluginFskey) use (&$status) {
            if (! $this->activate($pluginFskey)) {
                $status = false;
            }
        });

        return $status;
    }

    public function activate(?string $pluginFskey = null)
    {
        $plugin = new Plugin($pluginFskey);
        $fskey = $plugin->getStudlyName();

        event('plugin:activating', [[
            'fskey' => $fskey,
        ]]);

        if ($plugin->activate()) {
            $this->info(sprintf('Plugin %s activated successfully', $pluginFskey));

            event('plugin:activated', [[
                'fskey' => $fskey,
            ]]);

            return true;
        }

        $this->error(sprintf('Plugin %s activation failed', $pluginFskey));

        return false;
    }
}
