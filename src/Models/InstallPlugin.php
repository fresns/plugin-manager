<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Models;

use Fresns\PluginManager\Enums\PluginStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property string $name
 * @property mixed $status
 * @property string $kebab_name
 * @property string $alias
 */
class InstallPlugin extends Model
{
    /**
     * @inheritdoc
     */
    protected $guarded = [];

    /**
     * @inheritdoc
     */
    public $casts = [
        'composer' => 'json',
        'status' => 'integer',
    ];

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeEnable(Builder $query): Builder
    {
        return $query->where('status', PluginStatus::enable());
    }

    public function getStatusAttribute(int $status)
    {
        return PluginStatus::make(intval($status));
    }

    public function setStatusAttribute(PluginStatus $value)
    {
        $this->attributes['status'] = $value->value;
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeDisable(Builder $query): Builder
    {
        return $query->where('status', PluginStatus::disable());
    }

    /**
     * @return string
     */
    public function getKebabNameAttribute(): string
    {
        return Str::kebab($this->name);
    }
}
