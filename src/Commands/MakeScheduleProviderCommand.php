<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeScheduleProviderCommand extends GeneratorCommand
{
    use Traits\StubTrait;

    protected $signature = 'make:schedule-provider {name=ScheduleServiceProvider}';

    protected $description = 'Generate a schedule service provider for specified plugin';

    protected function getStubName(): string
    {
        return 'schedule-provider';
    }
}
