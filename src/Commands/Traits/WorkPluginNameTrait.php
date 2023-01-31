<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands\Traits;

trait WorkPluginNameTrait
{
    public function getPluginName()
    {
        $pluginName = $this->argument('name');
        if (! $pluginName) {
            $pluginName = basename(getcwd());
        }

        return $pluginName;
    }
}
