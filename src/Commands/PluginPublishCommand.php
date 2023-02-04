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
    use Traits\WorkPluginNameTrait;

    protected $signature = 'plugin:publish {name?}';

    protected $description = 'Distribute static resources of the plugin';

    public function handle()
    {
        $pluginName = $this->getPluginName();
        $plugin = new Plugin($pluginName);

        if ($this->validatePluginRootPath($plugin)) {
            $this->error('Failed to operate plugins root path');

            return Command::FAILURE;
        }

        if (! $plugin->isValidPlugin()) {
            return Command::FAILURE;
        }

        File::cleanDirectory($plugin->getAssetsPath());
        File::copyDirectory($plugin->getAssetsSourcePath(), $plugin->getAssetsPath());

        $this->info("Published: {$plugin->getUnikey()}");

        return Command::SUCCESS;
    }
}
