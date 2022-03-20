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

class PluginMakeControllerCommand extends GeneratorCommand
{
    use PluginCommandTrait;

    /**
     * The name of argument being used.
     *
     * @var string
     */
    protected string $argumentName = 'controller';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:make-controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new restful controller for the specified plugin.';

    /**
     * Get controller name.
     *
     * @return string
     */
    public function getDestinationFilePath(): string
    {
        $path = $this->getPlugin()->getPath().'/';

        $controllerPath = GenerateConfigReader::read('controller');

        return $path.$controllerPath->getPath().'/'.$this->getControllerName().'.php';
    }

    /**
     * @return string
     */
    protected function getTemplateContents(): string
    {
        $plugin = $this->getPlugin();

        return (new Stub($this->getStubName(), [
            'PLUGIN' => $this->getPluginName(),
            'CONTROLLER_NAME' => $this->getControllerName(),
            'NAMESPACE' => $plugin->getStudlyName(),
            'CLASS_NAMESPACE' => $this->getClassNamespace($plugin),
            'CLASS' => $this->getControllerNameWithoutNamespace(),
            'KEBAB_NAME' => $plugin->getKebabName(),
            'NAME' => $this->getPluginName(),
            'STUDLY_NAME' => $plugin->getStudlyName(),
            'PLUGIN_NAMESPACE' => $this->laravel['plugins.repository']->config('namespace'),
        ]))->render();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['controller', InputArgument::REQUIRED, 'The name of the controller class.'],
            ['plugin', InputArgument::OPTIONAL, 'The name of plugins will be used.'],
        ];
    }

    /**
     * @return string
     */
    protected function getControllerName(): string
    {
        $controller = Str::studly($this->argument('controller'));

        if (Str::contains(strtolower($controller), 'controller') === false) {
            $controller .= 'Controller';
        }

        return $controller;
    }

    /**
     * @return string
     */
    private function getControllerNameWithoutNamespace(): string
    {
        return class_basename($this->getControllerName());
    }

    public function getDefaultNamespace(): string
    {
        $repository = $this->laravel['plugins.repository'];

        return $repository->config('paths.generator.controller.namespace') ?: $repository->config('paths.generator.controller.path', 'Http/Controllers');
    }

    /**
     * Get the stub file name based on the options.
     *
     * @return string
     */
    protected function getStubName(): string
    {
        return '/controller.stub';
    }
}
