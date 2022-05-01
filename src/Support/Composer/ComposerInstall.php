<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Support\Composer;

use Fresns\PluginManager\Exceptions\ComposerException;

class ComposerInstall extends Composer
{
    public function beforeRun(): void
    {
        $this->setRequires($requires = $this->repository->getComposerRequires('require'));
        $this->setDevRequires($this->repository->getComposerRequires('require-dev')->notIn($requires));
    }

    public function afterRun(): void
    {
        $failedRequires = $this->filterExistRequires($this->getRequires()->merge($this->getDevRequires()));

        if ($failedRequires->notEmpty()) {
            info('composer install fail: ' . $failedRequires);
            throw new ComposerException("Package {$failedRequires} installation failed");
        }
    }
}
