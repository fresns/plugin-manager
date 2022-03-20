<?php

namespace Fresns\PluginManager\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ThemeUninstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:uninstall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uninstall a theme.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->filesystem = app('files');
        $this->pluginRepository = app('plugins.repository');

        $plugin = $this->pluginRepository
            ->addLocation(config('plugins.paths.themes'))
            ->findOrFail($themeName = $this->argument('theme'));
        $plugin->setClearData($this->option('cleardata'));

        $plugin->delete();

        $this->info("Theme $themeName uninstalled.");

        return 0;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['theme', InputArgument::REQUIRED, 'The name of theme.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['cleardata', null, InputArgument::OPTIONAL, 'Trigger clear plugin data.', false],
        ];
    }
}
