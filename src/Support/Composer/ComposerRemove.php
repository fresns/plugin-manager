<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Support\Composer;

use Fresns\PluginManager\Exceptions\ComposerException;
use Fresns\PluginManager\ValueObjects\ValRequires;

class ComposerRemove extends Composer
{
    protected array $removePluginRequires = [];

    public function appendRemovePluginRequires($pluginName, ValRequires $removeRequires): self
    {
        $currentPlugin = $this->repository->findOrFail($pluginName);
        $notRemoveRequires = $removeRequires->notIn($currentPlugin->getAllComposerRequires());

        if ($notRemoveRequires->notEmpty()) {
            throw new ComposerException("Package $notRemoveRequires is not in the plugin $pluginName.");
        }

        $this->removePluginRequires[$currentPlugin->getName()] = $removeRequires;

        return $this;
    }

    /**
     * @return array
     */
    public function getRemovePluginRequires(): array
    {
        return $this->removePluginRequires;
    }

    /**
     * @return ValRequires
     */
    public function getRemoveRequiresByPlugins(): ValRequires
    {
        $pluginNames = array_keys($this->getRemovePluginRequires());

        $valRequires = ValRequires::make();
        $removePluginRequires = array_reduce($this->getRemovePluginRequires(), function (ValRequires $valRequires, ValRequires $removePluginRequires) {
            return $valRequires->merge($removePluginRequires);
        }, $valRequires);

        if ($relyOtherPluginRemoveRequires = $this->repository->getExceptPluginNameComposerRequires($pluginNames)) {
            return $removePluginRequires->notIn($relyOtherPluginRemoveRequires);
        }

        return $removePluginRequires;
    }

    public function beforeRun(): void
    {
        if ($this->getRemovePluginRequires()) {
            $this->appendRemoveRequires($this->getRemoveRequiresByPlugins());
        }
    }

    public function afterRun(): void
    {
        $failedRequires = $this->getRemoveRequires()->in($this->getExistRequires())->unique();

        if ($failedRequires->notEmpty()) {
            throw new ComposerException("Package {$failedRequires} remove failed");
        }
    }
}
