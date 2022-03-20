<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Listeners\PluginUninstall;

use Fresns\PluginManager\Support\Plugin;
use Illuminate\Support\Facades\Artisan;

class PluginDeactivate
{
    public function handle(Plugin $plugin)
    {
        Artisan::call('plugin:deactivate', ['plugin' => $plugin->getName()]);
    }
}
