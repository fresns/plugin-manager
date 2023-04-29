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

class PluginListCommand extends Command
{
    protected $signature = 'plugin:list';

    protected $description = 'Get the list of installed plugins';

    public function handle()
    {
        $pluginDir = config('plugins.paths.plugins', 'extensions/plugins');

        $pluginDirs = File::glob(sprintf('%s/*', rtrim($pluginDir, '/')));

        $rows = [];
        foreach ($pluginDirs as $pluginDir) {
            if (! is_dir($pluginDir)) {
                continue;
            }

            $pluginUnikey = basename($pluginDir);

            $plugin = new Plugin($pluginUnikey);

            $rows[] = $plugin->getPluginInfo();
        }

        $this->table([
            'Plugin Name',
            'Validation',
            'Available',
            'Plugin Status',
            'Assets Status',
            'Plugin Path',
            'Assets Path',
        ], $rows);

        return Command::SUCCESS;
    }

    public function replaceDir(?string $path)
    {
        if (! $path) {
            return null;
        }

        return ltrim(str_replace(base_path(), '', $path), '/');
    }
}
