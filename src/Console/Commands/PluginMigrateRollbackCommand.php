<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\Support\Migrations\Migrator;
use Fresns\PluginManager\Traits\MigrationLoaderTrait;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PluginMigrateRollbackCommand extends Command
{
    use MigrationLoaderTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:migrate-rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the plugins migrations.';

    /**
     * @var RepositoryInterface
     */
    protected $plugin;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->plugin = $this->laravel['plugins.repository'];

        $name = $this->argument('plugin');

        if (! empty($name)) {
            $this->rollback($name);

            return 0;
        }

        foreach ($this->plugin->getOrdered($this->option('direction')) as $plugin) {
            $this->line('Running for plugin: <info>'.$plugin->getName().'</info>');

            $this->rollback($plugin);
        }

        return 0;
    }

    /**
     * Rollback migration from the specified plugin.
     *
     * @param $plugin
     */
    public function rollback($plugin)
    {
        if (is_string($plugin)) {
            $plugin = $this->plugin->findOrFail($plugin);
        }

        $migrator = new Migrator($plugin, $this->getLaravel());

        $database = $this->option('database');

        if (! empty($database)) {
            $migrator->setDatabase($database);
        }

        $migrated = $migrator->rollback();

        if (count($migrated)) {
            foreach ($migrated as $migration) {
                $this->line("Rollback: <info>{$migration}</info>");
            }

            return;
        }

        $this->comment('Nothing to rollback.');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['plugin', InputArgument::OPTIONAL, 'The name of plugin will be used.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['direction', 'd', InputOption::VALUE_OPTIONAL, 'The direction of ordering.', 'desc'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
        ];
    }
}
