<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Support\Json;
use Fresns\PluginManager\Support\Zip;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class PluginUnzipCommand extends Command
{
    protected $signature = 'plugin:unzip {path}';

    protected $description = 'Unzip the package to the plugin directory';

    public function handle()
    {
        $this->zip = new Zip();

        $tmpDirPath = $this->zip->unpack($this->argument('path'));

        $pluginJsonPath = "{$tmpDirPath}/plugin.json";
        if (! file_exists($tmpDirPath)) {
            \info($message = 'Plugin file does not exist: '.$pluginJsonPath);
            $this->error('install plugin error '.$message);
            return Command::FAILURE;
        }

        $plugin = Json::make($pluginJsonPath);

        $pluginUnikey = $plugin->get('unikey');
        if (! $pluginUnikey) {
            \info('Failed to get plugin unikey: '.var_export($pluginUnikey, true));
            $this->error('install plugin error, plugin.json is invalid plugin json');
            return Command::FAILURE;
        }

        $pluginDir = sprintf('%s/%s',
            config('plugins.paths.plugins'),
            $pluginUnikey
        );

        if (file_exists($pluginDir)) {
            $this->backup($pluginDir, $pluginUnikey);
        }

        File::copyDirectory($tmpDirPath, $pluginDir);
        File::deleteDirectory($tmpDirPath);

        Cache::put('install:plugin_unikey', $pluginUnikey, now()->addMinutes(5));

        return Command::SUCCESS;
    }

    public function backup(string $pluginDir, string $pluginUnikey)
    {
        $backupDir = config('plugins.paths.backups');

        File::ensureDirectoryExists($backupDir);

        $dirs = File::glob("$backupDir/$pluginUnikey*");

        $currentBackupCount = count($dirs);

        $targetPath = sprintf('%s/%s-%s-%s', $backupDir, $pluginUnikey, date('YmdHis'), $currentBackupCount + 1);

        File::copyDirectory($pluginDir, $targetPath);
        File::cleanDirectory($pluginDir);

        return true;
    }
}
