<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

class ThemeActivateCommand extends PluginActivateCommand
{
    protected $name = 'theme:activate';

    public function handle(): int
    {
        return $this->call('plugin:activate', [
            'plugin' => $this->argument('plugin'),
        ]);
    }
}
