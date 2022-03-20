<?php

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
