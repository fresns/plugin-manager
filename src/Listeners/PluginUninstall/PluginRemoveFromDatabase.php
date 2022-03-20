<?php

namespace Fresns\PluginManager\Listeners\PluginUninstall;

use Fresns\PluginManager\Listeners\PluginEventFilter;
use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Models\Plugin as PluginModel;
use Fresns\PluginManager\Support\PluginConstant;

class PluginRemoveFromDatabase extends PluginEventFilter
{
    protected $type = PluginConstant::PLUGIN_TYPE_EXTENSION;

    public function handleEvent(Plugin $plugin)
    {
        return PluginModel::withTrashed()->where('unikey', $plugin->getName())->forceDelete();
    }
}
