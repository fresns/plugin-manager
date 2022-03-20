<?php

namespace Fresns\PluginManager\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Fresns\PluginManager\Support\Plugin;

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
        /**
         * check if user entred an argument.
         */
        if ($this->argument('plugin') === null) {
            $this->activateAll();
            return 0;
        }

        /** @var Plugin $plugin */
        $plugin = $this->laravel['plugins.repository']->findOrFail($this->argument('plugin'));

        if ($plugin->isDisabled()) {
            $plugin->activate();

            $this->info("Plugin [{$plugin}] activate successful.");
        } else {
            $this->comment("Plugin [{$plugin}] has already activate.");
        }

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
            if ($plugin->isDisabled()) {
                $plugin->activate();
                $this->info("Plugin [{$plugin}]  activate successful.");
            } else {
                $this->comment("Plugin [{$plugin}] has already activate.");
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
