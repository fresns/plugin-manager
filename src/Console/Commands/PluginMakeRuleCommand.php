<?php

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Support\Config\GenerateConfigReader;
use Fresns\PluginManager\Support\Stub;
use Fresns\PluginManager\Traits\PluginCommandTrait;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class PluginMakeRuleCommand extends GeneratorCommand
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
    protected $name = 'plugin:make-rule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new validation rule for the specified plugin.';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the rule class.'],
            ['plugin', InputArgument::OPTIONAL, 'The name of plugin will be used.'],
        ];
    }

    public function getDefaultNamespace(): string
    {
        $repository = $this->laravel['plugins.repository'];

        return $repository->config('paths.generator.rules.namespace') ?: $repository->config('paths.generator.rules.path', 'Rules');
    }

    /**
     * @return string
     */
    protected function getTemplateContents(): string
    {
        $plugin = $this->getPlugin();

        return (new Stub('/rule.stub', [
            'NAMESPACE' => $this->getClassNamespace($plugin),
            'CLASS' => $this->getFileName(),
        ]))->render();
    }

    /**
     * @return string
     */
    protected function getDestinationFilePath(): string
    {
        $path = $this->getPlugin()->getPath() . '/';

        $rulePath = GenerateConfigReader::read('rules');

        return $path . $rulePath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return string
     */
    private function getFileName(): string
    {
        return Str::studly($this->argument('name'));
    }
}
