<?php

if (!function_exists('plugin_path')) {
    function plugin_path(string $name, string $path = ''): string
    {
        $plugin = app('plugins.repository')->find($name);

        if (is_null($plugin)) {
            $debugInfo = debug_backtrace(2, 1);
            [$file, $line] = array_values(head($debugInfo));

            $pathInfo = pathinfo($file);
            $fileWithoutExt = sprintf('%s/%s', $pathInfo['dirname'], $pathInfo['filename']);

            throw new \RuntimeException("Plugin $name notfound. call at file $fileWithoutExt:$line");
        }

        return $plugin->getPath().($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}
