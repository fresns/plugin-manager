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

class PluginDeactivateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:deactivate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate the specified plugin.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        /**
         * check if user entred an argument.
         */
        if ($this->argument('plugin') === null) {
            $this->deactivateAll();

            return 0;
        }

        /** @var Plugin $plugin */
        $plugin = $this->laravel['plugins.repository']->findOrFail($this->argument('plugin'));

        if ($plugin->isEnabled()) {
            $plugin->deactivate();

            $this->info("Plugin [{$plugin}] deactivated successful.");
        } else {
            $this->comment("Plugin [{$plugin}] has already deactivated.");
        }

        return 0;
    }

    /**
     * deactivateAll.
     *
     * @return void
     */
    public function deactivateAll(): void
    {
        $plugins = $this->laravel['plugins.repository']->all();
        /** @var Plugin $plugin */
        foreach ($plugins as $plugin) {
            if ($plugin->isEnabled()) {
                $plugin->deactivate();

                $this->info("Plugin [{$plugin}] deactivated successful.");
            } else {
                $this->comment("Plugin [{$plugin}] has already deactivated.");
            }
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
