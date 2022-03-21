<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Support\ThemeAddToDatabase;

class ThemePublishCommand extends PluginPublishCommand
{
    protected $name = 'theme:publish';

    public function handle(): int
    {
        $plugin = $this->laravel['plugins.repository']->findOrFail($this->argument('plugin'));
        $this->publish($plugin);

        return 0;
    }

    public function saveToDatabase(Plugin $plugin)
    {
        // write json data to database
        (new ThemeAddToDatabase())->handle($plugin);
    }
}
