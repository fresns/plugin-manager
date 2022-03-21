<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

class ThemePublishCommand extends PluginPublishCommand
{
    protected $name = 'theme:publish';

    public function handle(): int
    {
        return $this->call('plugin:publish', [
            'plugin' => $this->argument('plugin'),
        ]);
    }
}
