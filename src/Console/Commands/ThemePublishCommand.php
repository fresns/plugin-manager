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

class ThemePublishCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Write the configuration information of the theme theme to the database and distribute static resources.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('theme');

        $plugin = app('plugins.repository')
            ->addLocation(config('plugins.paths.themes'))
            ->findOrFail($name);

        $this->publish($plugin);

        return 0;
    }

    /**
     * Publish assets from the specified plugin.
     *
     * @param  Plugin  $plugin
     */
    public function publish(Plugin $plugin): void
    {
        with(new AssetPublisher($plugin))
            ->setRepository(app('plugins.repository'))
            ->setConsole($this)
            ->publish();

        $this->line("<info>Theme Published</info>: {$plugin->getStudlyName()}");
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
}
