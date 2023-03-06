<?php

if (!function_exists('plugin_path')) {
    function plugin_path(string $unikey) {
        return rtrim(config('plugins.paths.plugins'), '/') . DIRECTORY_SEPARATOR . $unikey;
    }
}
