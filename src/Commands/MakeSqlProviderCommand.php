<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeSqlProviderCommand extends GeneratorCommand
{
    use Traits\StubTrait;

    protected $signature = 'make:sql-provider {fskey=SqlLogServiceProvider}';

    protected $description = 'Generate a sql log service provider for specified plugin and need your manual register it.';

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
        return 'app/Providers/sql-provider';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace."\Providers";
    }
}
