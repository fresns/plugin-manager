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

class PluginListCommand extends Command
{
    protected $signature = 'plugin:list';

    protected $description = 'Get the list of installed plugins';

    public function handle()
    {
        $pluginDir = config('plugins.paths.plugins');

        $pluginDirs = File::glob(sprintf('%s/*', rtrim($pluginDir, '/')));

        $rows = [];
        foreach ($pluginDirs as $pluginDir) {
            if (! is_dir($pluginDir)) {
                continue;
            }

            $pluginName = basename($pluginDir);

            $plugin = new Plugin($pluginName);

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

        return 0;
    }

    public function replaceDir(?string $path)
    {
        if (! $path) {
            return null;
        }

        return ltrim(str_replace(base_path(), '', $path), '/');
    }
}
