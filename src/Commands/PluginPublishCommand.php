<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PluginPublishCommand extends Command
{
    protected $signature = 'plugin:publish {name}';

    protected $description = 'Distribute static resources of the plugin';

    public function handle()
    {
        $plugin = new Plugin($this->argument('name'));

        if (!$plugin->isValidPlugin()) {
            return 0;
        }

        File::cleanDirectory($plugin->getAssetsPath());
        File::copyDirectory($plugin->getAssetsSourcePath(), $plugin->getAssetsPath());

        $this->info("Published: {$plugin->getUnikey()}");

        return 0;
    }
}
