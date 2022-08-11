<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Illuminate\Console\Command;

class PluginMakeCommand extends Command
{
    protected $signature = 'plugin:make {name}
        {--force}
        ';

    protected $description = 'Alias of new command';

    public function handle()
    {
        $this->call('new', [
            'name' => $this->argument('name'),
            '--force' => $this->option('force'),
        ]);
    }
}
