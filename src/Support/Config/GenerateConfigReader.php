<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Support\Config;

class GenerateConfigReader
{
    public static function read(string $value): GeneratorPath
    {
        return new GeneratorPath(config("plugins.paths.generator.$value"));
    }
}
