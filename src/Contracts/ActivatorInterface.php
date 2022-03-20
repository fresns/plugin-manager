<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Contracts;

use Fresns\PluginManager\Support\Plugin;

interface ActivatorInterface
{
    /**
     * Enables a Plugin.
     *
     * @param  Plugin  $plugin
     */
    public function enable(Plugin $plugin): void;

    /**
     * Disables a Plugin.
     *
     * @param  Plugin  $plugin
     */
    public function disable(Plugin $plugin): void;

    /**
     * Determine whether the given status same with a Plugin status.
     *
     * @param  Plugin  $plugin
     * @param  bool  $status
     * @return bool
     */
    public function hasStatus(Plugin $plugin, bool $status): bool;

    /**
     * Set active state for a Plugin.
     *
     * @param  Plugin  $plugin
     * @param  bool  $active
     */
    public function setActive(Plugin $plugin, bool $active): void;

    /**
     * Sets a Plugin status by its name.
     *
     * @param  string  $name
     * @param  bool  $active
     */
    public function setActiveByName(string $name, bool $active): void;

    /**
     * Deletes a Plugin activation status.
     *
     * @param  Plugin  $plugin
     */
    public function delete(Plugin $plugin): void;

    /**
     * Deletes any Plugin activation statuses created by this class.
     */
    public function reset(): void;
}
