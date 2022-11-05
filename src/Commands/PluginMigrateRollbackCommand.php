<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Illuminate\Console\Command;

class PluginMigrateRollbackCommand extends Command
{
    protected $signature = 'plugin:migrate-rollback {name}
        {--database=}
        {--force=}
        {--realpath=}
        {--pretend=}
        {--step=}
        ';

    protected $description = 'Rollback the latest migration of the plugin';

    public function handle()
    {
        $plugin = new Plugin($this->argument('name'));

        if (! $plugin->isValidPlugin()) {
            return 0;
        }

        try {
            $path = $plugin->getMigratePath();
            if (glob("$path/*")) {
                $this->call('migrate:rollback', [
                    '--database' => $this->option('database'),
                    '--force' => $this->option('force') ?? true,
                    '--path' => $plugin->getMigratePath(),
                    '--realpath' => $this->option('realpath') ?? true,
                    '--step' => $this->option('step'),
                    '--pretend' => $this->option('pretend') ?? false,
                ]);

                $this->info("Migrate Rollback: {$plugin->getUnikey()}");
            } else {
                $this->info('Migrate Rollback: Nothing need to rollback');
            }
        } catch (\Throwable $e) {
            $this->warn("Migrate Rollback {$plugin->getUnikey()} fail\n");
            $this->error($e->getMessage());
        }

        return 0;
    }
}
