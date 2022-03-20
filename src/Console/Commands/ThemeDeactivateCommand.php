<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Support\PluginConstant;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ThemeDeactivateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:deactivate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate a theme.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $themeName = $this->argument('theme');

        $plugin = new Plugin(app(), $themeName, null, PluginConstant::PLUGIN_TYPE_THEME);
        $plugin->deactivate();

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
}
