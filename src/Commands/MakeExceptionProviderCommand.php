<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeExceptionProviderCommand extends GeneratorCommand
{
    use Traits\StubTrait;

    protected $signature = 'make:exception-provider {fskey=ExceptionServiceProvider}';

    protected $description = 'Generate a exception provider for specified plugin.';

    public function handle()
    {
        $path = $this->getPath('Providers/'.$this->getNameInput());
        $pluginFskey = basename(dirname($path, 3));
        $pluginJsonPath = dirname($path, 3).'/plugin.json';

        parent::handle();

        if (is_file($pluginJsonPath)) {
            $this->installPluginProviderAfter(
                $this->getPluginJsonSearchContent($pluginFskey),
                $this->getPluginJsonReplaceContent($this->getNameInput(), $pluginFskey),
                $pluginJsonPath
            );
        }
    }

    protected function getStubName(): string
    {
        return 'app/Providers/exception-provider';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace."\Providers";
    }
}
