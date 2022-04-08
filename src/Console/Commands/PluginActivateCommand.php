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

class PluginActivateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:activate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate the specified plugin.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->argument('plugin') === null) {
            $this->activateAll();

            return 0;
        }

        /** @var Plugin $plugin */
        $plugin = $this->laravel['plugins.repository']->findOrFail($this->argument('plugin'));

        $plugin->activate();

        $this->info("Plugin [{$plugin}] activate successful.");

        return 0;
    }

    /**
     * activateAll.
     */
    public function activateAll()
    {
        /** @var Plugin[] $plugins */
        $plugins = $this->laravel['plugins.repository']->all();

        foreach ($plugins as $plugin) {
            $plugin->activate();
            $this->info("Plugin [{$plugin}]  activate successful.");
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['plugin', InputArgument::OPTIONAL, 'Plugin name.'],
        ];
    }
}
