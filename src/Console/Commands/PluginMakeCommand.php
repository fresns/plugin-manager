<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Exception;
use Fresns\PluginManager\Contracts\ActivatorInterface;
use Fresns\PluginManager\Support\Generators\PluginGenerator;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PluginMakeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new plugin.';

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle(): int
    {
        $names = $this->argument('name');
        $success = true;

        foreach ($names as $name) {
            $code = with(new PluginGenerator($name))
                ->setFilesystem($this->laravel['files'])
                ->setPluginRepository($this->laravel['plugins.repository'])
                ->setConfig($this->laravel['config'])
                ->setActivator($this->laravel[ActivatorInterface::class])
                ->setConsole($this)
                ->setForce($this->option('force'))
                ->setActive(!$this->option('disabled'))
                ->generate();

            if ($code === E_ERROR) {
                $success = false;
            }
        }

        return $success ? 0 : E_ERROR;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::IS_ARRAY, 'The names of plugin will be created.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['disabled', 'd', InputOption::VALUE_NONE, 'Do not enable the plugin at creation.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when the plugin already exists.'],
        ];
    }
}
