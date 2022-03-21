<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Listeners\PluginInstall;

use Illuminate\Support\Facades\Artisan;
use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Support\PluginConstant;

class PluginUnzip
{
    public function handle(Plugin $plugin)
    {
        $command = 'plugin:unzip';
        if ($plugin->getType() === PluginConstant::PLUGIN_TYPE_THEME) {
            $command = 'theme:unzip';
        }

        Artisan::call($command, [
            'path' => $plugin->getPath(),
        ]);
    }
}
