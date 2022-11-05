<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Illuminate\Console\Command;

class PluginMigrateRefreshCommand extends Command
{
    protected $signature = 'plugin:migrate-refresh {name}
        {--database=}
        {--force=}
        {--realpath=}
        {--seed=}
        {--seeder=}
        {--step=}
        ';

    protected $description = 'Reset and rerun the plugin migration';

    public function handle()
    {
        $plugin = new Plugin($this->argument('name'));

        if (! $plugin->isValidPlugin()) {
            return 0;
        }

        try {
            $this->call('migrate:refresh', [
                '--database' => $this->option('database'),
                '--force' => $this->option('force') ?? true,
                '--path' => $plugin->getMigratePath(),
                '--realpath' => $this->option('realpath') ?? true,
                '--step' => $this->option('step'),
            ]);

            if ($this->option('seed')) {
                $this->call('plugin:seed', [
                    '--class' => $this->option('seeder'),
                    '--database' => $this->option('database'),
                    '--force' => $this->option('force') ?? true,
                ]);
            }

            $this->info("Migrate Refresh: {$plugin->getUnikey()}");
        } catch (\Throwable $e) {
            $this->warn("Migrate Refresh {$plugin->getUnikey()} fail\n");
            $this->error($e->getMessage());
        }

        return 0;
    }
}
