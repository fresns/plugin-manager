<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeSqlLogProviderCommand extends GeneratorCommand
{
    use Traits\StubTrait;

    protected $signature = 'make:sql-log-provider {name=SqlLogServiceProvider}';

    protected $description = 'Generate a schedule service provider for specified plugin';

    protected function getStubName(): string
    {
        return 'sql-provider';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace."\Providers";
    }
}
