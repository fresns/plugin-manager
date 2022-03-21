<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Listeners\PluginUninstall;

use Fresns\PluginManager\Support\Plugin;
use Illuminate\Support\Facades\Artisan;

class PluginComposerRemove
{
    public function handle(Plugin $plugin)
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
