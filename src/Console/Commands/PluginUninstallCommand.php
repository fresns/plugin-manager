<?php

namespace Fresns\PluginManager\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Fresns\PluginManager\Support\Plugin;
use Symfony\Component\Console\Input\InputArgument;
use Fresns\PluginManager\Traits\PluginCommandTrait;

class PluginUninstallCommand extends Command
{
    use PluginCommandTrait;

    protected $name = 'plugin:uninstall';

    protected $description = 'Uninstall a plugin from the application';

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
