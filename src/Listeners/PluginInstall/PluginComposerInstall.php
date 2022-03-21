<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Listeners\PluginInstall;

use Illuminate\Support\Facades\Artisan;
use Fresns\PluginManager\Support\Plugin;

class PluginComposerInstall
{
    public function handle(Plugin $plugin)
    {
        Artisan::call('plugin:composer-install');
    }
}
