<?php

namespace Fresns\PluginManager\Listeners\PluginInstall;

use Fresns\PluginManager\Listeners\PluginEventFilter;
use Illuminate\Support\Facades\Artisan;
use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Support\PluginConstant;

class ThemeUnzip extends PluginEventFilter
{
    protected $type = PluginConstant::PLUGIN_TYPE_THEME;

    public function handleEvent(Plugin $plugin)
    {
        Artisan::call('theme:unzip', [
            'path' => $plugin->getPath(),
        ]);
    }
}
