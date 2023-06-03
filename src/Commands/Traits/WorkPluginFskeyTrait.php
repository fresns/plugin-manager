<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands\Traits;

trait WorkPluginFskeyTrait
{
    public function getPluginFskey(): mixed
    {
        $pluginFskey = $this->argument('fskey');
        if (! $pluginFskey) {
            $pluginRootPath = config('plugins.paths.plugins');
            if (str_contains(getcwd(), $pluginRootPath)) {
                $pluginFskey = basename(getcwd());
            }
        }

        return $pluginFskey;
    }

    public function validatePluginRootPath($plugin): bool
    {
        $pluginRootPath = config('plugins.paths.plugins');
        $currentPluginRootPath = rtrim($plugin->getPluginPath(), '/');

        return $pluginRootPath == $currentPluginRootPath;
    }
}
