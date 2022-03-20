<?php

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Support\Config\GenerateConfigReader;
use Fresns\PluginManager\Support\Stub;
use Fresns\PluginManager\Traits\PluginCommandTrait;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PluginMakeTestCommand extends GeneratorCommand
{
    use PluginCommandTrait;

    protected string $argumentName = 'name';

    protected $name = 'plugin:make-test';

    protected $description = 'Create a new test class for the specified plugin.';

    public function getDefaultNamespace(): string
    {
        $repository = $this->laravel['plugins.repository'];

        if ($this->option('feature')) {
            return $repository->config('paths.generator.test-feature.namespace') ?: $repository->config('paths.generator.test-feature.path', 'Tests/Feature');
        }

        return $repository->config('paths.generator.test.namespace') ?: $repository->config('paths.generator.test.path', 'Tests/Unit');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the form request class.'],
            ['plugin', InputArgument::OPTIONAL, 'The name of plugin will be used.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['feature', false, InputOption::VALUE_NONE, 'Create a feature test.'],
        ];
    }

    /**
     * @return string
     */
    protected function getTemplateContents(): string
    {
        $plugin = $this->getPlugin();

        return (new Stub($this->getStubName(), [
            'NAMESPACE' => $this->getClassNamespace($plugin),
            'CLASS' => $this->getClass(),
        ]))->render();
    }

    /**
     * @return string
     */
    protected function getDestinationFilePath(): string
    {
        $path = $this->getPlugin()->getPath() . '/';

        if ($this->option('feature')) {
            $testPath = GenerateConfigReader::read('test-feature');
        } else {
            $testPath = GenerateConfigReader::read('test');
        }

        return $path . $testPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name'));
    }

    private function getStubName()
    {
        if ($this->option('feature')) {
            return '/feature-test.stub';
        }

        return '/unit-test.stub';
    }
}
