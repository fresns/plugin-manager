<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Illuminate\Console\Command;

class CustomCommand extends Command
{
    protected $signature = 'custom';

    protected $description = 'Customize plugin namespace or others by config/plugins.php';

    public function handle()
    {
        $to = config_path('plugins.php');

        if (file_exists($to)) {
            $this->error('config/plugins.php is already existed');

            return Command::FAILURE;
        }

        $from = dirname(__DIR__, 2).'/config/plugins.php';

        copy($from, $to);

        $this->line('<info>Config file copied to </info> <comment>['.$to.']</comment>');

        return Command::SUCCESS;
    }
}
