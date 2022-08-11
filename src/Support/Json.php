<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Support;

use Illuminate\Support\Arr;

class Json
{
    protected $filepath;

    protected $data = [];

    public function __construct(?string $filepath = null)
    {
        $this->filepath = $filepath;

        $this->decode();
    }

    public static function make(?string $filepath = null)
    {
        return new static($filepath);
    }

    public function decode(?string $content = null)
    {
        if ($this->filepath && file_exists($this->filepath)) {
            $content = @file_get_contents($this->filepath);
        }

        if (!$content) {
            $content = '';
        }

        $this->data = json_decode($content, true) ?? [];

        return $this;
    }

    public function encode(?array $data = null, $options = null)
    {
        $defaultOptions = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT;

        if ($options) {
            $options = $defaultOptions | $options;
        } else {
            $options = $defaultOptions;
        }

        if ($data) {
            $this->data = $data;
        }

        return json_encode($this->data, $options);
    }

    public function get(mixed $key = null, $default = null)
    {
        if (!Arr::has($this->data, $key)) {
            return $this->data;
        }

        return Arr::get($this->data, $key, $default);
    }
}
