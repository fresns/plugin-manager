<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * Class PluginStatus.
 *
 * @method static self enable()
 * @method static self disable()
 */
class PluginStatus extends Enum
{
    protected static function values(): array
    {
        return [
            'disable' => 0,
            'enable' => 1,
        ];
    }
}
