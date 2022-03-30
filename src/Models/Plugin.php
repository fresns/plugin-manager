<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plugin extends Model
{
    use SoftDeletes;

    public const PLUGIN_TYPE_DEACTIVATE = 0;
    public const PLUGIN_TYPE_ACTIVATE = 1;
    public const PLUGIN_TYPE_MAP = [
        Plugin::PLUGIN_TYPE_DEACTIVATE => 'Deactivate',
        Plugin::PLUGIN_TYPE_ACTIVATE => 'Activate',
    ];

    protected $guarded = [];

    protected $casts = [
        'scene' => 'json',
    ];

    public static function boot()
    {
        parent::boot();
    }
}
