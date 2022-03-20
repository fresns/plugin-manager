<?php

namespace Fresns\PluginManager\Listeners\PluginActivate;

use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Models\Plugin as PluginModel;

class PluginActivateFromDatabase
{
    public function handle(Plugin $plugin)
    {
        PluginModel::withTrashed()->where('unikey', $plugin->getName())->update([
            'is_enable' => true,
        ]);
    }
}
