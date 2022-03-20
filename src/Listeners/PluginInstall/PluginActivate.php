<?php

namespace Fresns\PluginManager\Listeners\PluginInstall;

use Fresns\PluginManager\Listeners\PluginEventFilter;
use Illuminate\Support\Facades\Artisan;
use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Support\PluginConstant;

class PluginActivate extends PluginEventFilter
{
    protected $type = PluginConstant::PLUGIN_TYPE_EXTENSION;

    public function handleEvent(Plugin $plugin)
    {
        Artisan::call('plugin:activate', ['plugin' => $plugin->getName()]);
    }
}
