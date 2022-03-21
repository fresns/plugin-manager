<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Support\PluginConstant;

class ThemeInstallCommand extends PluginInstallCommand
{
    protected $name = 'theme:install';

    public function handle(): int
    {
        return $this->call('plugin:install', [
            'path' => $this->argument('path'),
            'plugin' => $this->argument('plugin'),
            '--type' => PluginConstant::PLUGIN_TYPE_THEME,
            '--disabled' => $this->option('disabled'),
            '--force' => $this->option('force'),
        ]);
    }
}
