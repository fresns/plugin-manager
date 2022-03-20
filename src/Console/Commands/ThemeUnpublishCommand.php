<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Support\Publishing\AssetPublisher;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ThemeUnpublishCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:unpublish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unpublish a theme\'s assets of the application';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $plugin = $this->laravel['plugins.repository']
            ->addLocation(config('plugins.paths.themes'))
            ->findOrFail($this->argument('theme'));
        $this->unpublish($plugin);

        return 0;
    }

    public function unpublish(Plugin $plugin): void
    {
        with(new AssetPublisher($plugin))
            ->setRepository($this->laravel['plugins.repository'])
            ->setConsole($this)
            ->unpublish();

        $this->line("<info>Unpublished</info>: {$plugin->getStudlyName()}");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['theme', InputArgument::REQUIRED, 'The name of theme will be used.'],
        ];
    }
}
