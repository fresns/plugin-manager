<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Illuminate\Console\Command;

class PluginMigrateResetCommand extends Command
{
    use Traits\WorkPluginFskeyTrait;

    protected $signature = 'plugin:migrate-reset {fskey?}
        {--database=}
        {--force=}
        {--realpath=}
        {--pretend=}
        ';

    protected $description = 'Rollback of all migrations of the plugin';

    public function handle()
    {
        $pluginFskey = $this->getPluginFskey();
        $plugin = new Plugin($pluginFskey);

        if (! $plugin->isValidPlugin()) {
            return Command::FAILURE;
        }

        if ($plugin->isDeactivate()) {
            return Command::FAILURE;
        }

        try {
            $this->call('migrate:reset', [
                '--database' => $this->option('database'),
                '--force' => $this->option('force') ?? true,
                '--path' => $plugin->getMigratePath(),
                '--realpath' => $this->option('realpath') ?? true,
                '--pretend' => $this->option('pretend') ?? false,
            ]);

            $this->info("Migrate Reset: {$plugin->getFskey()}");
        } catch (\Throwable $e) {
            $this->warn("Migrate Reset {$plugin->getFskey()} fail\n");
            $this->error($e->getMessage());
        }

        return Command::SUCCESS;
    }
}
