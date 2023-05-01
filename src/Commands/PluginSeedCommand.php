<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Illuminate\Console\Command;

class PluginSeedCommand extends Command
{
    use Traits\WorkPluginFskeyTrait;

    protected $signature = 'plugin:seed {fskey?}
        {--class=DatabaseSeeder}
        {--database=}
        {--force=}
        ';

    protected $description = 'Run plugin migration';

    public function handle()
    {
        $pluginFskey = $this->getPluginFskey();
        $plugin = new Plugin($pluginFskey);

        if (! $plugin->isValidPlugin()) {
            return Command::FAILURE;
        }

        try {
            $class = $plugin->getSeederNamespace().$this->option('class');

            if (class_exists($class)) {
                $this->call('db:seed', [
                    'class' => $class,
                    '--database' => $this->option('database'),
                    '--force' => $this->option('force') ?? true,
                ]);
            }

            $this->info("Seed: {$plugin->getFskey()}");
        } catch (\Throwable $e) {
            $this->warn("Seed {$plugin->getFskey()} fail\n");
            $this->error($e->getMessage());
        }

        return Command::SUCCESS;
    }
}
