<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Illuminate\Console\Command;

class CustomCommand extends Command
{
    protected $signature = 'custom';

    protected $description = 'Customize extensions namespace or others by config/plugins.php';

    public function handle()
    {
        $to = config_path('plugins.php');

        if (file_exists($to)) {
            $this->error('config/plugins.php is already existed');
            return 0;
        }

        $from = dirname(__DIR__, 2) . '/config/plugins.php';

        copy($from, $to);

        $this->line('<info>Config file copied to </info> <comment>[' . $to . ']</comment>');

        return 0;
    }
}
