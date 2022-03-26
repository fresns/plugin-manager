<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class PluginMakeCmdWordProviderCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:cmd-word-provider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a cmd word service provider for specified plugin.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        return $this->call('plugin:make-provider', [
            'name' => 'CmdWordServiceProvider',
            'plugin' => $this->argument('plugin'),
            '--cmd-word' => true,
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
