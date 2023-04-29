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
    use Traits\WorkPluginUnikeyTrait;

    protected $signature = 'plugin:activate {unikey?}';

    protected $description = 'Activate Plugin';

    public function handle()
    {
        $pluginUnikey = $this->getPluginUnikey();

        if ($pluginUnikey) {
            $this->activate($pluginUnikey);
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

        collect($plugin->all())->map(function ($pluginUnikey) {
            $this->activate($pluginUnikey);
        });
    }

    public function activate(?string $pluginUnikey = null)
    {
        $plugin = new Plugin($pluginUnikey);

        $unikey = $plugin->getStudlyName();

        event('plugin:activating', [[
            'unikey' => $unikey,
        ]]);

        if ($result = $plugin->activate()) {
            $this->info(sprintf('Plugin %s activate successfully', $pluginUnikey));
        } else {
            $this->error(sprintf('Plugin %s activate failure', $pluginUnikey));
        }

        event('plugin:activated', [[
            'unikey' => $unikey,
        ]]);

        return $result;
    }
}
