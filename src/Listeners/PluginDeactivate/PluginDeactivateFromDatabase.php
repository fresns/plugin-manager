<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Listeners\PluginDeactivate;

use Fresns\PluginManager\Models\Plugin as PluginModel;
use Fresns\PluginManager\Support\Plugin;

class PluginDeactivateFromDatabase
{
    public function handle(Plugin $plugin)
    {
        PluginModel::withTrashed()->where('unikey', $plugin->getName())->update([
            'is_enable' => false,
        ]);
    }
}
