<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Illuminate\Console\Command;

class PluginMigrateCommand extends Command
{
    use Traits\WorkPluginFskeyTrait;

    protected $signature = 'plugin:migrate {fskey?}
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
        $pluginFskey = $this->getPluginFskey();

        if ($pluginFskey) {
            return $this->migrate($pluginFskey);
        } else {
            $plugin = new Plugin();

            collect($plugin->all())->map(function ($pluginFskey) {
                $this->migrate($pluginFskey, true);
            });
        }

        return Command::SUCCESS;
    }

    public function migrate(string $pluginFskey, $isAll = false)
    {
        $plugin = new Plugin($pluginFskey);

        if (! $plugin->isValidPlugin()) {
            return Command::FAILURE;
        }

        if ($plugin->isDeactivate() && $isAll) {
            return Command::FAILURE;
        }

        try {
            $this->call('migrate', [
                '--database' => $this->option('database'),
                '--force' => $this->option('force') ?? true,
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
                    '--force' => $this->option('force') ?? true,
                ]);
            }

            $this->info("Migrated: {$plugin->getFskey()}");
        } catch (\Throwable $e) {
            $this->warn("Migrated {$plugin->getFskey()} fail\n");
            $this->error($e->getMessage());
        }
    }
}
