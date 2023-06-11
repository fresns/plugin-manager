<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
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
        $zip = new Zip();

        $tmpDirPath = $zip->unpack($this->argument('path'));

        if (! is_dir($tmpDirPath)) {
            $this->error("install plugin error, plugin unzip dir doesn't exists: {$tmpDirPath}");

            return Command::FAILURE;
        }

        $pluginJsonPath = "{$tmpDirPath}/plugin.json";
        if (! file_exists($tmpDirPath)) {
            \info($message = 'Plugin file does not exist: '.$pluginJsonPath);
            $this->error('install plugin error '.$message);

            return Command::FAILURE;
        }

        $plugin = Json::make($pluginJsonPath);

        $pluginFskey = $plugin->get('fskey');
        if (! $pluginFskey) {
            \info('Failed to get plugin fskey: '.var_export($pluginFskey, true));
            $this->error('install plugin error, plugin.json is invalid plugin json');

            return Command::FAILURE;
        }

        $pluginDir = sprintf('%s/%s',
            config('plugins.paths.plugins'),
            $pluginFskey
        );

        if (file_exists($pluginDir)) {
            $this->backup($pluginDir, $pluginFskey);
        }

        File::copyDirectory($tmpDirPath, $pluginDir);
        File::deleteDirectory($tmpDirPath);

        Cache::put('install:plugin_fskey', $pluginFskey, now()->addMinutes(5));

        return Command::SUCCESS;
    }

    public function backup(string $pluginDir, string $pluginFskey)
    {
        $backupDir = config('plugins.paths.backups');

        File::ensureDirectoryExists($backupDir);

        if (! is_file($backupDir.'/.gitignore')) {
            file_put_contents($backupDir.'/.gitignore', '*'.PHP_EOL.'!.gitignore');
        }

        $dirs = File::glob("$backupDir/$pluginFskey*");

        $currentBackupCount = count($dirs);

        $targetPath = sprintf('%s/%s-%s-%s', $backupDir, $pluginFskey, date('YmdHis'), $currentBackupCount + 1);

        File::copyDirectory($pluginDir, $targetPath);
        File::cleanDirectory($pluginDir);

        return true;
    }
}
