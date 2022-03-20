<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Listeners\PluginUninstall;

use Fresns\PluginManager\Listeners\PluginEventFilter;
use Fresns\PluginManager\Models\Plugin as PluginModel;
use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Support\PluginConstant;

class ThemeRemoveFromDatabase extends PluginEventFilter
{
    protected $type = PluginConstant::PLUGIN_TYPE_THEME;

    public function handleEvent(Plugin $plugin)
    {
        return PluginModel::withTrashed()->where('unikey', $plugin->getName())->forceDelete();
    }
}
