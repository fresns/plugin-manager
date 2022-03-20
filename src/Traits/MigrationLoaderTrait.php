<?php

namespace Fresns\PluginManager\Traits;

use Fresns\PluginManager\Support\Config\GenerateConfigReader;

trait MigrationLoaderTrait
{
    /**
     * Include all migrations files from the specified plugin.
     *
     * @param string $plugin
     */
    protected function loadMigrationFiles($plugin)
    {
        $path = $this->laravel['plugins.repository']->getPluginPath($plugin) . $this->getMigrationGeneratorPath();

        $files = $this->laravel['files']->glob($path . '/*_*.php');

        foreach ($files as $file) {
            $this->laravel['files']->requireOnce($file);
        }
    }

    /**
     * Get migration generator path.
     *
     * @return string
     */
    protected function getMigrationGeneratorPath()
    {
        return GenerateConfigReader::read('migration')->getPath();
    }
}
