<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Listeners\PluginInstall;

use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Support\PluginConstant;
use Illuminate\Support\Facades\Artisan;

class PluginPublish
{
    public function handle(Plugin $plugin)
    {
        $command = 'plugin:publish';
        if ($plugin->getType() === PluginConstant::PLUGIN_TYPE_THEME) {
            $command = 'theme:publish';
        }

        Artisan::call($command, [
            'plugin' => $plugin->getName(),
        ]);
    }
}
