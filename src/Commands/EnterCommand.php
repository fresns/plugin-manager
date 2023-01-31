<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Illuminate\Console\Command;

class EnterCommand extends Command
{
    protected $signature = 'enter {unikey}';

    protected $description = 'Go to plugin directory';

    public function handle()
    {
        $pluginRootPath = config('plugins.paths.plugins');
        if (!$pluginRootPath) {
            $this->error('Plugin directory not retrieved');
            return Command::FAILURE;
        }

        $unikey = $this->argument('unikey');

        $pluginPath = "{$pluginRootPath}/{$unikey}";
        if (!file_exists($pluginPath)) {
            $this->error("Plugin directory {$unikey} does not exist");
            return Command::FAILURE;
        }

        if (getenv("PWD") != $pluginPath) {
            $this->warn("Go to the plugin {$unikey} directory");
            $this->line('');
            $this->warn('Please input this command on your terminal:');

            $command = sprintf('cd %s', $pluginPath);
            $this->line($command);
            $this->line('');
        } else {
            $this->info("Currently in the plugin {$unikey} directory");
            $this->line($pluginPath);

            $this->line('');
            $this->info("Now you can run command in your plugin: {$unikey}");
            $this->line('fresns');
        }

        return Command::SUCCESS;
    }
}
