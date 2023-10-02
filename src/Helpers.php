<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

// plugin_path
if (! function_exists('plugin_path')) {
    // Defines the function 'plugin_path'
    function plugin_path(string $path)
    {
        return rtrim(config('plugins.paths.plugins'), '/').DIRECTORY_SEPARATOR.ltrim($path, '/');
    }
}

// plugin_assets
if (! function_exists('plugin_assets')) {
    // Defines the function 'plugin_assets'
    function plugin_assets(string $path)
    {
        return rtrim(config('plugins.paths.assets'), '/').DIRECTORY_SEPARATOR.ltrim($path, '/');
    }
}
