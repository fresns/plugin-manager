<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Support\Config\GenerateConfigReader;
use Fresns\PluginManager\Support\Stub;
use Fresns\PluginManager\Traits\PluginCommandTrait;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class PluginMakeDTOCommand extends GeneratorCommand
{
    use PluginCommandTrait;

    protected string $argumentName = 'dto';

    protected $name = 'plugin:make-dto';

    protected $description = 'Create a new DTO for the specified plugin.';

    protected function getArguments(): array
    {
        return [
            ['dto', InputArgument::REQUIRED, 'The name of the controller class.'],
            ['plugin', InputArgument::REQUIRED, 'The name of plugins will be used.'],
        ];
    }

    public function getDefaultNamespace(): string
    {
        $repository = $this->laravel['plugins.repository'];

        return $repository->config('paths.generator.dto.namespace') ?: $repository->config('paths.generator.dto.path', 'DTO');
    }

    protected function getTemplateContents(): string
    {
        $plugin = $this->getPlugin();

        return (new Stub('/dto.stub', [
            'NAMESPACE' => $this->getClassNamespace($plugin),
            'CLASS' => $this->getClass(),
        ]))->render();
    }

    protected function getDestinationFilePath(): string
    {
        $path = $this->getPlugin()->getPath().'/';

        $dtoPath = GenerateConfigReader::read('dto');

        return $path.$dtoPath->getPath().'/'.$this->getFileName().'.php';
    }

    /**
     * @return string
     */
    protected function getFileName(): string
    {
        return Str::studly($this->argument('dto'));
    }
}
