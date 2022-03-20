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

class PluginMakeEventCommand extends GeneratorCommand
{
    use PluginCommandTrait;

    protected string $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:make-event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new event class for the specified plugin';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the event.'],
            ['plugin', InputArgument::OPTIONAL, 'The name of plugin will be used.'],
        ];
    }

    public function getDefaultNamespace(): string
    {
        $repository = $this->laravel['plugins.repository'];

        return $repository->config('paths.generator.event.namespace') ?: $repository->config('paths.generator.event.path', 'Events');
    }

    public function getTemplateContents(): string
    {
        $plugin = $this->getPlugin();

        return (new Stub('/event.stub', [
            'NAMESPACE' => $this->getClassNamespace($plugin),
            'CLASS' => $this->getClass(),
        ]))->render();
    }

    public function getDestinationFilePath(): string
    {
        $path = $this->getPlugin()->getPath().'/';

        $eventPath = GenerateConfigReader::read('event');

        return $path.$eventPath->getPath().'/'.$this->getFileName().'.php';
    }

    /**
     * @return string
     */
    protected function getFileName(): string
    {
        return Str::studly($this->argument('name'));
    }
}
