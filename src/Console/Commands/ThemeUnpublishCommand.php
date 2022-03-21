<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

class ThemeUnpublishCommand extends PluginUnpublishCommand
{
    protected $name = 'theme:unpublish';

    public function handle(): int
    {
        return $this->call('plugin:unpublish', [
            'plugin' => $this->argument('plugin'),
        ]);
    }
}
