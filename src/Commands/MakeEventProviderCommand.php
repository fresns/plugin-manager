<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeEventProviderCommand extends GeneratorCommand
{
    use Traits\StubTrait;

    protected $signature = 'make:event-provider {unikey=EventServiceProvider}';

    protected $description = 'Generate an event service provider for specified plugin';

    protected function getStubName(): string
    {
        return 'event-provider';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace."\Providers";
    }
}
