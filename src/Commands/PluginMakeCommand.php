<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Illuminate\Console\Command;

class PluginMakeCommand extends Command
{
    protected $signature = 'plugin:make {fskey}
        {--force}
        ';

    protected $description = 'Alias of new command';

    public function handle()
    {
        return $this->call('new', [
            'fskey' => $this->argument('fskey'),
            '--force' => $this->option('force'),
        ]);
    }
}
