<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Illuminate\Console\Command;

class BackCommand extends Command
{
    protected $signature = 'back';

    protected $description = 'Back to the root directory';

    public function handle()
    {
        $basePath = base_path();
        if (getenv('PWD') != base_path()) {
            $this->warn('Back to the root directory');
            $this->line('');
            $this->warn('Please input this command on your terminal:');

            $command = sprintf('cd %s', $basePath);
            $this->line($command);
            $this->line('');
        } else {
            $this->info('Currently in the root directory');
            $this->line($basePath);

            $this->line('');
            $this->info('Now you can run command:');
            $this->line('fresns or php artisan');
        }

        return Command::SUCCESS;
    }
}
