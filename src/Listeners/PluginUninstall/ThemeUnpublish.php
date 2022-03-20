<?php

namespace Fresns\PluginManager\Listeners\PluginUninstall;

use Illuminate\Support\Facades\Artisan;
use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Support\PluginConstant;
use Fresns\PluginManager\Listeners\PluginEventFilter;

class ThemeUnpublish extends PluginEventFilter
{
    protected $type = PluginConstant::PLUGIN_TYPE_THEME;

    public function handleEvent(Plugin $plugin)
    {
        Artisan::call('theme:unpublish', [
            'theme' => $plugin->getName(),
        ]);
    }
}
