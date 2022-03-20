<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Listeners\PluginUninstall;

use Fresns\PluginManager\Listeners\PluginEventFilter;
use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Support\PluginConstant;
use Illuminate\Support\Facades\Artisan;

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
