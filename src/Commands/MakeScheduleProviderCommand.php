<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeScheduleProviderCommand extends GeneratorCommand
{
    use Traits\StubTrait;

    protected $signature = 'make:schedule-provider {fskey=ScheduleServiceProvider}';

    protected $description = 'Generate a schedule service provider for specified plugin';

    protected function getStubName(): string
    {
        return 'schedule-provider';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace."\Providers";
    }
}
