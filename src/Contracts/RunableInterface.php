<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Contracts;

interface RunableInterface
{
    /**
     * Run the specified command.
     *
     * @param  string  $command
     */
    public function run(string $command);
}
