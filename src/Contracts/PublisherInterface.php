<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Contracts;

interface PublisherInterface
{
    /**
     * Publish something.
     */
    public function publish(): void;
}
