<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Support\Publishing;

use Fresns\PluginManager\Support\Config\GenerateConfigReader;
use Fresns\PluginManager\Support\PluginConstant;

class AssetPublisher extends Publisher
{
    /**
     * Determine whether the result message will shown in the console.
     *
     * @var bool
     */
    protected bool $showMessage = false;

    /**
     * Get destination path.
     *
     * @return string
     */
    public function getDestinationPath(): string
    {
        return $this->repository->assetPath($this->plugin->getStudlyName());
    }

    /**
     * Get source path.
     *
     * @return string
     */
    public function getSourcePath(): string
    {
        $path = GenerateConfigReader::read('assets')->getPath();
        if ($this->getPlugin()->getType() === PluginConstant::PLUGIN_TYPE_THEME) {
            $path = 'assets';
        }

        return $this->getPlugin()->getExtraPath($path);
    }
}
