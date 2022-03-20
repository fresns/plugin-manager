<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class PluginMakeConsoleProviderCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:console-provider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service provider class for the console.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        return $this->call('plugin:make-provider', [
            'name' => 'ConsoleServiceProvider',
            'plugin' => $this->argument('plugin'),
            '--console' => true,
        ]);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['plugin', InputArgument::REQUIRED, 'Plugin name.'],
        ];
    }
}
