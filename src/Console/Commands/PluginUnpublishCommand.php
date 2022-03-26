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

class PluginUnpublishCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:unpublish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uninstall the static resources for the specified plugin.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $plugin = $this->laravel['plugins.repository']->findOrFail($this->argument('plugin'));
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
            ['plugin', InputArgument::REQUIRED, 'The name of plugin will be used.'],
        ];
    }
}
