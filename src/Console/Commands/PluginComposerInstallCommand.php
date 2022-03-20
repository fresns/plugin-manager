<?php

namespace Fresns\PluginManager\Console\Commands;

use Illuminate\Console\Command;
use Fresns\PluginManager\Support\Composer\ComposerInstall;

class PluginComposerInstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:composer-install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all plugins composer package.';

    public function handle(): void
    {
        try {
            ComposerInstall::make()->run();
            $this->info('Composer install complete.');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
