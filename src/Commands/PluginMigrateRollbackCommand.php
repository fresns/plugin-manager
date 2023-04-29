<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Illuminate\Console\Command;

class PluginMigrateRollbackCommand extends Command
{
    use Traits\WorkPluginUnikeyTrait;

    protected $signature = 'plugin:migrate-rollback {unikey?}
        {--database=}
        {--force=}
        {--realpath=}
        {--pretend=}
        {--step=}
        ';

    protected $description = 'Rollback the latest migration of the plugin';

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
            $path = $plugin->getMigratePath();
            if (glob("$path/*")) {
                $exitCode = $this->call('migrate:reset', [
                    '--database' => $this->option('database'),
                    '--force' => $this->option('force') ?? true,
                    '--path' => $plugin->getMigratePath(),
                    '--realpath' => $this->option('realpath') ?? true,
                    '--pretend' => $this->option('pretend') ?? false,
                ]);

                $this->info("Migrate Rollback: {$plugin->getUnikey()}");
                $this->info('Migrate Rollback Path: '.str_replace(base_path().'/', '', $path));

                if ($exitCode != 0) {
                    return $exitCode;
                }
            } else {
                $this->info('Migrate Rollback: Nothing need to rollback');
            }
        } catch (\Throwable $e) {
            $this->warn("Migrate Rollback {$plugin->getUnikey()} fail\n");
            $this->error($e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
