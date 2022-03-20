<?php

namespace Fresns\PluginManager\Listeners\PluginUninstall;

use Illuminate\Support\Facades\Artisan;
use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Support\PluginConstant;
use Fresns\PluginManager\Listeners\PluginEventFilter;

class PluginComposerRemove extends PluginEventFilter
{
    protected $type = PluginConstant::PLUGIN_TYPE_EXTENSION;

    public function handleEvent(Plugin $plugin)
    {
        Artisan::call('plugin:composer-remove', [
            'plugin' => $plugin->getName(),
            'packages' => $this->getAllPackageNames($plugin),
        ]);
    }

    public function getAllPackageNames(Plugin $plugin)
    {
        $packages = $plugin->getAllComposerRequires()->toArray();

        $data = [];
        foreach ($packages as $package) {
            $data[] = $package->name;
        }

        return $data;
    }
}
