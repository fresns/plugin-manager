<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Listeners\PluginInstall;

use Fresns\PluginManager\Listeners\PluginEventFilter;
use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Support\PluginConstant;
use Illuminate\Support\Facades\Artisan;

class ThemePublish extends PluginEventFilter
{
    protected $type = PluginConstant::PLUGIN_TYPE_THEME;

    public function handleEvent(Plugin $plugin)
    {
        Artisan::call('theme:publish', [
            'theme' => $plugin->getName(),
        ]);
    }
}
