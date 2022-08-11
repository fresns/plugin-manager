<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Illuminate\Console\Command;

class PluginSeedCommand extends Command
{
    protected $signature = 'plugin:seed {name}
        {--class=DatabaseSeeder}
        {--database=}
        {--force=}
        ';

    protected $description = 'Run plugin migration';

    public function handle()
    {
        $plugin = new Plugin($this->argument('name'));

        if (! $plugin->isValidPlugin()) {
            return 0;
        }

        try {
            $class = $plugin->getSeederNamespace().$this->option('class');

            $this->call('db:seed', [
                'class' => $class,
                '--database' => $this->option('database'),
                '--force' => $this->option('force'),
            ]);

            $this->info("Seed: {$plugin->getUnikey()}");
        } catch (\Throwable $e) {
            $this->warn("Seed {$plugin->getUnikey()} fail\n");
            $this->error($e->getMessage());
        }

        return 0;
    }
}
