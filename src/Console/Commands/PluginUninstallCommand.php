<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Exception;
use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Traits\PluginCommandTrait;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class PluginUninstallCommand extends Command
{
    use PluginCommandTrait;

    protected $name = 'plugin:uninstall';

    protected $description = 'Uninstall the plugin and select whether you want to clean the data of the plugin.';

    public function handle(): int
    {
        try {
            $plugin = app('plugins.repository')->findOrFail($this->argument('plugin'));
            $plugin->setClearData($this->option('cleardata'));

            /** @var Plugin $plugin */
            $plugin->delete();

            $this->info("Plugin {$this->argument('plugin')} has been deleted.");

            return 0;
        } catch (Exception $exception) {
            $this->error($exception->getMessage());

            return E_ERROR;
        }
    }

    protected function getArguments(): array
    {
        return [
            ['plugin', InputArgument::REQUIRED, 'The name of plugin to delete.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['cleardata', null, InputArgument::OPTIONAL, 'Trigger clear plugin data.', false],
        ];
    }
}
