<?php

namespace Fresns\PluginManager\Listeners\PluginUninstall;

use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Support\PluginConstant;
use Fresns\PluginManager\Listeners\PluginEventFilter;
use Fresns\PluginManager\Models\Plugin as PluginModel;

class ThemeRemoveFromDatabase extends PluginEventFilter
{
    protected $type = PluginConstant::PLUGIN_TYPE_THEME;

    public function handleEvent(Plugin $plugin)
    {
        return PluginModel::withTrashed()->where('unikey', $plugin->getName())->forceDelete();
    }
}
