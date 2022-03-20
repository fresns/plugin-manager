<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Listeners;

use Fresns\PluginManager\Support\Plugin;

abstract class PluginEventFilter
{
    protected $type;

    public function getType(): int
    {
        return $this->type;
    }

    public function handle(Plugin $plugin)
    {
        if ($this->getType() !== $plugin->getType()) {
            return;
        }

        $this->handleEvent($plugin);
    }

    abstract public function handleEvent(Plugin $plugin);
}
