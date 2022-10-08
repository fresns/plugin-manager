<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Illuminate\Console\Command;

class PluginMigrateCommand extends Command
{
    protected $signature = 'plugin:migrate {name?}
        {--database=}
        {--force=}
        {--realpath=}
        {--schema-path=}
        {--seed=}
        {--seeder=}
        {--step=}
        {--pretend=}
        ';

    protected $description = 'Run plugin migration';

    public function handle()
    {
        if ($pluginName = $this->argument('name')) {
            $this->migrate($pluginName);
        } else {
            $plugin = new Plugin();

            collect($plugin->all())->map(function ($pluginName) {
                $this->migrate($pluginName);
            });
        }

        return 0;
    }

    public function migrate(string $pluginName)
    {
        $plugin = new Plugin($pluginName);

        if (! $plugin->isValidPlugin()) {
            return 0;
        }

        try {
            $this->call('migrate', [
                '--database' => $this->option('database'),
                '--force' => $this->option('force') ?? false,
                '--path' => $plugin->getMigratePath(),
                '--realpath' => $this->option('realpath') ?? true,
                '--schema-path' => $this->option('schema-path'),
                '--pretend' => $this->option('pretend') ?? false,
                '--step' => $this->option('step') ?? false,
            ]);

            if ($this->option('seed')) {
                $this->call('plugin:seed', [
                    '--class' => $this->option('seeder'),
                    '--database' => $this->option('database'),
                    '--force' => $this->option('force') ?? false,
                ]);
            }

            $this->info("Migrated: {$plugin->getUnikey()}");
        } catch (\Throwable $e) {
            $this->warn("Migrated {$plugin->getUnikey()} fail\n");
            $this->error($e->getMessage());
        }
    }
}
