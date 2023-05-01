<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeCmdWordProviderCommand extends GeneratorCommand
{
    use Traits\StubTrait;

    protected $signature = 'make:cmd-word-provider {fskey=CmdWordServiceProvider}';

    protected $description = 'Generate a cmd word service provider for specified plugin';

    protected function getStubName(): string
    {
        return 'cmd-word-provider';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace."\Providers";
    }
}
