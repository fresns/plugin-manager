<?php

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Support\Config\GenerateConfigReader;
use Fresns\PluginManager\Support\Stub;
use Fresns\PluginManager\Traits\PluginCommandTrait;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class PluginMakePolicyCommand extends GeneratorCommand
{
    use PluginCommandTrait;

    protected string $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:make-policy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new policy class for the specified plugin.';

    public function getDefaultNamespace(): string
    {
        $repository = $this->laravel['plugins.repository'];

        return $repository->config('paths.generator.policies.namespace') ?: $repository->config('paths.generator.policies.path', 'Policies');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the policy class.'],
            ['plugin', InputArgument::OPTIONAL, 'The name of plugin will be used.'],
        ];
    }

    /**
     * @return string
     */
    protected function getTemplateContents(): string
    {
        $plugin = $this->getPlugin();

        return (new Stub('/policy.plain.stub', [
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

        $policyPath = GenerateConfigReader::read('policies');

        return $path . $policyPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name'));
    }
}
