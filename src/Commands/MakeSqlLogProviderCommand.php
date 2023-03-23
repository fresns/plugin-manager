<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeSqlLogProviderCommand extends GeneratorCommand
{
    use Traits\StubTrait;

    protected $signature = 'make:sql-log-provider {name=SqlLogServiceProvider}';

    protected $description = 'Generate a sql log service provider for specified plugin and need your manual reigster it.';

    protected function getStubName(): string
    {
        return 'sql-provider';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace."\Providers";
    }
}
