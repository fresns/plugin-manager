<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PluginPublishCommand extends Command
{
    use Traits\WorkPluginUnikeyTrait;

    protected $signature = 'plugin:publish {unikey?}';

    protected $description = 'Distribute static resources of the plugin';

    public function handle()
    {
        $pluginUnikey = $this->getPluginUnikey();
        $plugin = new Plugin($pluginUnikey);

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
