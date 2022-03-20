<?php

namespace Fresns\PluginManager\Listeners\PluginDeactivate;

use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Models\Plugin as PluginModel;

class PluginDeactivateFromDatabase
{
    public function handle(Plugin $plugin)
    {
        PluginModel::withTrashed()->where('unikey', $plugin->getName())->update([
            'is_enable' => false,
        ]);
    }
}
