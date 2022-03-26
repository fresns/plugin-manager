<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Support\PluginConstant;

class ThemeUnzipCommand extends PluginUnzipCommand
{
    protected $name = 'theme:unzip';

    protected $description = 'Unzip the theme files into the /resources/themes/ directory.';

    public function handle(): int
    {
        return $this->call('plugin:unzip', [
            'path' => $this->argument('path'),
            '--type' => PluginConstant::PLUGIN_TYPE_THEME,
        ]);
    }
}
