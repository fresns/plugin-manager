<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Illuminate\Console\Command;

class PluginMigrateResetCommand extends Command
{
    protected $signature = 'plugin:migrate-reset {name}
        {--database=}
        {--force=}
        {--realpath=}
        {--pretend=}
        ';

    protected $description = 'Rollback of all migrations of the plugin';

    public function handle()
    {
        $plugin = new Plugin($this->argument('name'));

        if (! $plugin->isValidPlugin()) {
            return 0;
        }

        try {
            $this->call('migrate:reset', [
                '--database' => $this->option('database'),
                '--force' => $this->option('force') ?? true,
                '--path' => $plugin->getMigratePath(),
                '--realpath' => $this->option('realpath') ?? true,
                '--pretend' => $this->option('pretend') ?? false,
            ]);

            $this->info("Migrate Reset: {$plugin->getUnikey()}");
        } catch (\Throwable $e) {
            $this->warn("Migrate Reset {$plugin->getUnikey()} fail\n");
            $this->error($e->getMessage());
        }

        return 0;
    }
}
