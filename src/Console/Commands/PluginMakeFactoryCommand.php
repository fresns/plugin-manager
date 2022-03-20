<?php

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Traits\PluginCommandTrait;
use Illuminate\Support\Str;
use Fresns\PluginManager\Support\Config\GenerateConfigReader;
use Fresns\PluginManager\Support\Stub;
use Symfony\Component\Console\Input\InputArgument;

class PluginMakeFactoryCommand extends GeneratorCommand
{
    use PluginCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected string $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:make-factory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model factory for the specified plugin.';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the model.'],
            ['plugin', InputArgument::OPTIONAL, 'The name of plugin will be used.'],
        ];
    }


    /**
     * @return string
     */
    public function getDefaultNamespace(): string
    {
        $repository = $this->laravel['plugins.repository'];

        return $repository->config('paths.generator.factory.namespace') ?: $repository->config('paths.generator.factory.path', 'Database/Factories');
    }

    /**
     * @return string
     */
    protected function getTemplateContents(): string
    {
        $plugin = $this->getPlugin();

        return (new Stub('/factory.stub', [
            'NAMESPACE' => $this->getClassNamespace($plugin),
            'NAME' => $this->getStudlyName(),
        ]))->render();
    }

    /**
     * @return string
     */
    protected function getDestinationFilePath(): string
    {
        $path = $this->getPlugin()->getPath().'/';

        $factoryPath = GenerateConfigReader::read('factory');

        return $path . $factoryPath->getPath() . '/' . $this->getFileName().'.php';
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::contains($fileName = $this->getStudlyName(), 'Factory')
            ? $fileName
            : sprintf('%sFactory', $this->getStudlyName())
            ;
    }

    /**
     * @return mixed|string
     */
    private function getStudlyName()
    {
        return Str::studly($this->argument('name'));
    }
}
