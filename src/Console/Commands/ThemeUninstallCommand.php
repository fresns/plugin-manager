<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

class ThemeUninstallCommand extends PluginUninstallCommand
{
    protected $name = 'theme:uninstall';

    protected $description = 'Uninstall the theme and select whether you want to clean the data of the theme.';

    public function handle(): int
    {
        return $this->call('plugin:uninstall', [
            'plugin' => $this->argument('plugin'),
            '--cleardata' => $this->option('cleardata'),
        ]);
    }
}
