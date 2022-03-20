<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Listeners\PluginActivate;

use Fresns\PluginManager\Models\Plugin as PluginModel;
use Fresns\PluginManager\Support\Plugin;

class PluginActivateFromDatabase
{
    public function handle(Plugin $plugin)
    {
        PluginModel::withTrashed()->where('unikey', $plugin->getName())->update([
            'is_enable' => true,
        ]);
    }
}
