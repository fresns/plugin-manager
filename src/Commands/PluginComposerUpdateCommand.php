<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Support\Process;
use Illuminate\Console\Command;

class PluginComposerUpdateCommand extends Command
{
    protected $signature = 'plugin:composer-update';

    protected $description = 'Update all plugins composer package';

    public function handle()
    {
        $process = Process::run('composer update', $this->output);
        if (! $process->isSuccessful()) {
            $this->error('Failed to install packages, calc composer.json hash value fail');

            return Command::FAILURE;
        }

        return 0;
    }
}
