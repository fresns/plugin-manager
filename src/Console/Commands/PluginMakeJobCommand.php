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
use Symfony\Component\Console\Input\InputOption;

class PluginMakeJobCommand extends GeneratorCommand
{
    use PluginCommandTrait;

    protected string $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:make-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new job class for the specified plugin.';

    public function getDefaultNamespace(): string
    {
        $repository = $this->laravel['plugins.repository'];

        return $repository->config('paths.generator.jobs.namespace') ?: $repository->config('paths.generator.jobs.path', 'Jobs');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the job.'],
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
            ['sync', null, InputOption::VALUE_NONE, 'Indicates that job should be synchronous.'],
        ];
    }

    /**
     * Get template contents.
     *
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
     * Get the destination file path.
     *
     * @return string
     */
    protected function getDestinationFilePath(): string
    {
        $path = $this->getPlugin()->getPath().'/';

        $jobPath = GenerateConfigReader::read('jobs');

        return $path.$jobPath->getPath().'/'.$this->getFileName().'.php';
    }

    /**
     * @return string
     */
    private function getFileName(): string
    {
        return Str::studly($this->argument('name'));
    }

    /**
     * @return string
     */
    protected function getStubName(): string
    {
        if ($this->option('sync')) {
            return '/job.stub';
        }

        return '/job-queued.stub';
    }
}
