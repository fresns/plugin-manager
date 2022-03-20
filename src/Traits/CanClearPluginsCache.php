<?php

namespace Fresns\PluginManager\Traits;

trait CanClearPluginsCache
{
    /**
     * Clear the plugins cache if it is enabled.
     */
    public function clearCache(): void
    {
        if (config('plugins.cache.enabled') === true) {
            app('cache')->forget(config('plugins.cache.key'));
        }
    }
}
