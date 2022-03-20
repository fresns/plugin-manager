<?php

namespace Fresns\PluginManager\Listeners\PluginUninstall;

use Illuminate\Support\Facades\Artisan;
use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Support\PluginConstant;
use Fresns\PluginManager\Listeners\PluginEventFilter;

class PluginUnpublish extends PluginEventFilter
{
    protected $type = PluginConstant::PLUGIN_TYPE_EXTENSION;

    public function handleEvent(Plugin $plugin)
    {
        Artisan::call('plugin:unpublish', [
            'plugin' => $plugin->getName(),
        ]);
    }
}
