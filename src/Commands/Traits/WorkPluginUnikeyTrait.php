<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands\Traits;

trait WorkPluginUnikeyTrait
{
    public function getPluginUnikey()
    {
        $pluginUnikey = $this->argument('unikey');
        if (! $pluginUnikey) {
            $pluginRootPath = config('plugins.paths.plugins');
            if (str_contains(getcwd(), $pluginRootPath)) {
                $pluginUnikey = basename(getcwd());
            }
        }

        return $pluginUnikey;
    }

    public function validatePluginRootPath($plugin)
    {
        $pluginRootPath = config('plugins.paths.plugins');
        $currentPluginRootPath = rtrim($plugin->getPluginPath(), '/');

        return $pluginRootPath == $currentPluginRootPath;
    }
}
