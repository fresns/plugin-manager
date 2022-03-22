<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Listeners\PluginInstall;

use Fresns\PluginManager\Support\Plugin;

class PluginActivate
{
    public function handle(Plugin $plugin)
    {
        \Artisan::call('plugin:activate', ['plugin' => $plugin->getName()]);
    }
}
