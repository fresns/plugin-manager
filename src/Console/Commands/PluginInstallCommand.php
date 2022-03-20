<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Support\Plugin;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PluginInstallCommand extends Command
{
    protected $name = 'plugin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the plugin through the file directory.';

    /**
     * @return int
     */
    public function handle(): int
    {
        try {
            $plugin = new Plugin($this->getLaravel(), $this->argument('plugin'), $this->argument('path'));

            // force flag and disable flag need pass to oather event listener.
            $plugin->setForce($this->option('force'));
            $plugin->setDisable($this->option('disabled'));

            // trigger install flow

            // see config: event.installing
            $plugin->fireInstallingEvent();

            // see config: event.installed
            $plugin->fireInstalledEvent();

            return 0;
            // return $code;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());

            return E_ERROR;
        }
    }

    protected function getArguments(): array
    {
        return [
            ['path', InputArgument::REQUIRED, 'Local path.'],
            ['plugin', InputArgument::REQUIRED, 'Plugin name.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['disabled', 'd', InputOption::VALUE_NONE, 'Do not enable the plugin at creation.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when the plugin already exists.'],
        ];
    }
}
