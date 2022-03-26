<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Support\Config\GenerateConfigReader;
use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Support\Stub;
use Fresns\PluginManager\Traits\PluginCommandTrait;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PluginMakeListenerCommand extends GeneratorCommand
{
    use PluginCommandTrait;

    protected string $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:make-listener';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the given listener for the specified plugin.';

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the command.'],
            ['plugin', InputArgument::OPTIONAL, 'The name of plugin will be used.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['event', 'e', InputOption::VALUE_OPTIONAL, 'The event class being listened for.'],
            ['queued', null, InputOption::VALUE_NONE, 'Indicates the event listener should be queued.'],
        ];
    }

    public function getDefaultNamespace(): string
    {
        $repository = $this->laravel['plugins.repository'];

        return $repository->config('paths.generator.listener.namespace') ?: $repository->config('paths.generator.listener.path', 'Listeners');
    }

    protected function getTemplateContents(): string
    {
        $plugin = $this->getPlugin();

        return (new Stub($this->getStubName(), [
            'NAMESPACE' => $this->getClassNamespace($plugin),
            'EVENT_NAME' => $this->getEventName($plugin),
            'SHORT_EVENT_NAME' => $this->getShortEventName(),
            'CLASS' => $this->getClass(),
        ]))->render();
    }

    protected function getEventName(Plugin $plugin): string
    {
        $namespace = $this->laravel['plugins.repository']->config('namespace').'\\'.$plugin->getStudlyName();
        $eventPath = GenerateConfigReader::read('event');

        $eventName = $namespace.'\\'.$eventPath->getPath().'\\'.$this->option('event');

        return str_replace('/', '\\', $eventName);
    }

    protected function getShortEventName(): string
    {
        return class_basename($this->option('event'));
    }

    protected function getDestinationFilePath(): string
    {
        $path = $this->getPlugin()->getPath().'/';

        $listenerPath = GenerateConfigReader::read('listener');

        return $path.$listenerPath->getPath().'/'.$this->getFileName().'.php';
    }

    protected function getFileName(): string
    {
        return Str::studly($this->argument('name'));
    }

    protected function getStubName(): string
    {
        if ($this->option('queued')) {
            if ($this->option('event')) {
                return '/listener-queued.stub';
            }

            return '/listener-queued-duck.stub';
        }

        if ($this->option('event')) {
            return '/listener.stub';
        }

        return '/listener-duck.stub';
    }
}
