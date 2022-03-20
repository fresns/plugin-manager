<?php

namespace Fresns\PluginManager\Support;

/**
 * @see https://fresns.org/extensions/plugin/
 */
class PluginConstant
{
    public const PLUGIN_TYPE_EXTENSION = 1;
    public const PLUGIN_TYPE_APP = 2;
    public const PLUGIN_TYPE_ENGINE = 3;
    public const PLUGIN_TYPE_THEME = 4;
    public const PLUGIN_TYPE_MAP = [
        PluginConstant::PLUGIN_TYPE_EXTENSION => 'Function Extension',
        PluginConstant::PLUGIN_TYPE_APP => 'App Management',
        PluginConstant::PLUGIN_TYPE_ENGINE => 'Website Engine',
        PluginConstant::PLUGIN_TYPE_THEME => 'Engine Theme Template',
    ];
}
