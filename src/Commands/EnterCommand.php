<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Illuminate\Console\Command;

class EnterCommand extends Command
{
    protected $signature = 'enter {fskey}';

    protected $description = 'Go to plugin directory';

    public function handle()
    {
        $pluginRootPath = config('plugins.paths.plugins');
        if (! $pluginRootPath) {
            $this->error('Plugin directory not retrieved');

            return Command::FAILURE;
        }

        $fskey = $this->argument('fskey');

        $pluginPath = "{$pluginRootPath}/{$fskey}";
        if (! file_exists($pluginPath)) {
            $this->error("Plugin directory {$fskey} does not exist");

            return Command::FAILURE;
        }

        if (str_contains(strtolower(PHP_OS_FAMILY), 'win')) {
            $pluginPath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, $pluginPath);
        }

        if (getenv('PWD') != $pluginPath) {
            $this->warn("Go to the plugin {$fskey} directory");
            $this->line('');
            $this->warn('Please input this command on your terminal:');

            $command = sprintf('cd %s', $pluginPath);
            $this->line($command);
            $this->line('');
        } else {
            $this->info("Currently in the plugin {$fskey} directory");
            $this->line($pluginPath);

            $this->line('');
            $this->info("Now you can run command in your plugin: {$fskey}");
            $this->line('fresns');
        }

        return Command::SUCCESS;
    }
}
