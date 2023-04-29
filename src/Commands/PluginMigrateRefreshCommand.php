<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Illuminate\Console\Command;

class PluginMigrateRefreshCommand extends Command
{
    use Traits\WorkPluginUnikeyTrait;

    protected $signature = 'plugin:migrate-refresh {unikey?}
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
        $pluginUnikey = $this->getPluginUnikey();
        $plugin = new Plugin($pluginUnikey);

        if (! $plugin->isValidPlugin()) {
            return Command::FAILURE;
        }

        if ($plugin->isDeactivate()) {
            return Command::FAILURE;
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

        return Command::SUCCESS;
    }
}
