<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands\Traits;

trait WorkPluginNameTrait
{
    public function getPluginName()
    {
        $pluginName = $this->argument('name');
        if (! $pluginName) {
            $pluginRootPath = config('plugins.paths.plugins');
            if (str_contains(getcwd(), $pluginRootPath)) {
                $pluginName = basename(getcwd());
            }
        }

        return $pluginName;
    }

    public function validatePluginRootPath($plugin)
    {
        $pluginRootPath = config('plugins.paths.plugins');
        $currentPluginRootPath = rtrim($plugin->getPluginPath(), '/');

        return $pluginRootPath == $currentPluginRootPath;
    }
}
