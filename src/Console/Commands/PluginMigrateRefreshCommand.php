<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Traits\PluginCommandTrait;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PluginMigrateRefreshCommand extends Command
{
    use PluginCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:migrate-refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback & re-migrate the plugins migrations.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $plugin = $this->argument('plugin');

        if ($plugin && ! $this->getPluginName()) {
            $this->error("Plugin [$plugin] does not exists.");

            return E_ERROR;
        }

        $this->call('plugin:migrate-reset', [
            'plugin' => $this->getPluginName(),
            '--database' => $this->option('database'),
            '--force' => $this->option('force'),
        ]);

        $this->call('plugin:migrate', [
            'plugin' => $this->getPluginName(),
            '--database' => $this->option('database'),
            '--force' => $this->option('force'),
        ]);

        if ($this->option('seed')) {
            $this->call('plugin:seed', [
                'plugin' => $this->getPluginName(),
                '--force' => $this->option('force'),
            ]);
        }

        return 0;
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
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
        ];
    }

    public function getPluginName()
    {
        $plugin = $this->argument('plugin');

        if (! $plugin) {
            return null;
        }

        $plugin = app('plugins.repository')->find($plugin);

        return $plugin ? $plugin->getStudlyName() : null;
    }
}
