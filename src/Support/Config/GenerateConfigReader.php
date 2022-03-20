<?php

namespace Fresns\PluginManager\Support\Config;

class GenerateConfigReader
{
    public static function read(string $value): GeneratorPath
    {
        return new GeneratorPath(config("plugins.paths.generator.$value"));
    }
}
