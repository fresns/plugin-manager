<?php

namespace Fresns\PluginManager\Listeners\PluginInstall;

use Illuminate\Support\Facades\Artisan;
use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Support\PluginConstant;
use Fresns\PluginManager\Listeners\PluginEventFilter;

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
