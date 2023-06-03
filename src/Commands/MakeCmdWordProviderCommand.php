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

    protected $signature = 'make:cmdword-provider {fskey=CmdWordServiceProvider}';

    protected $description = 'Generate a cmd word service provider for specified plugin';

    public function handle()
    {
        $path = $this->getPath('Providers/'.$this->getNameInput());
        $pluginFskey = basename(dirname($path, 3));
        $pluginJsonPath = dirname($path, 3).'/plugin.json';

        $this->generateCmdWordService($pluginFskey);

        parent::handle();

        $this->installPluginProviderAfter(
            $this->getPluginJsonSearchContent($pluginFskey),
            $this->getPluginJsonReplaceContent($this->getNameInput(), $pluginFskey),
            $pluginJsonPath
        );
    }

    protected function getStubName(): string
    {
        return 'cmd-word-provider';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace."\Providers";
    }

    protected function generateCmdWordService($pluginFskey)
    {
        $path = $this->getPath('Services/CmdWordService');
        $dirpath = dirname($path);

        if (! is_dir($dirpath)) {
            @mkdir($dirpath, 0755, true);
        }

        if (! is_file($path)) {
            $stubPath = __DIR__.'/stubs/cmd-word-service.stub';

            $content = file_get_contents($stubPath);

            $newContent = str_replace('$STUDLY_NAME$', $pluginFskey, $content);

            file_put_contents($path, $newContent);
        }
    }
}
