<?php

namespace Fresns\PluginManager\Console\Commands;

use Illuminate\Support\Str;
use Fresns\PluginManager\Support\Config\GenerateConfigReader;
use Fresns\PluginManager\Support\Stub;
use Fresns\PluginManager\Traits\PluginCommandTrait;
use Symfony\Component\Console\Input\InputArgument;

class PluginMakeMailCommand extends GeneratorCommand
{
    use PluginCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:make-mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new email class for the specified plugin';

    protected string $argumentName = 'name';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the mailable.'],
            ['plugin', InputArgument::OPTIONAL, 'The name of plugin will be used.'],
        ];
    }

    public function getDefaultNamespace(): string
    {
        $repository = $this->laravel['plugins.repository'];

        return $repository->config('paths.generator.emails.namespace') ?: $repository->config('paths.generator.emails.path', 'Emails');
    }

    /**
     * Get template contents.
     *
     * @return string
     */
    protected function getTemplateContents(): string
    {
        $plugin = $this->getPlugin();

        return (new Stub('/mail.stub', [
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
        $path = $this->getPlugin()->getPath() . '/';

        $mailPath = GenerateConfigReader::read('emails');

        return $path . $mailPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name'));
    }
}
