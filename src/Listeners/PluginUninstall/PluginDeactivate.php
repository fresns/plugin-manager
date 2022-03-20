<?php

namespace Fresns\PluginManager\Listeners\PluginUninstall;

use Illuminate\Support\Facades\Artisan;
use Fresns\PluginManager\Support\Plugin;

class PluginDeactivate
{
    public function handle(Plugin $plugin)
    {
        Artisan::call('plugin:deactivate', ['plugin' => $plugin->getName()]);
    }
}
