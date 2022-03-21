<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

class ThemeDeactivateCommand extends PluginDeactivateCommand
{
    protected $name = 'theme:deactivate';

    public function handle(): int
    {
        return $this->call('plugin:deactivate', [
            'plugin' => $this->argument('plugin'),
        ]);
    }
}
