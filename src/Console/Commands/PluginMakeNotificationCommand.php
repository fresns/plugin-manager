<?php

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Traits\PluginCommandTrait;
use Illuminate\Support\Str;
use Fresns\PluginManager\Support\Config\GenerateConfigReader;
use Fresns\PluginManager\Support\Stub;
use Symfony\Component\Console\Input\InputArgument;

class PluginMakeNotificationCommand extends GeneratorCommand
{
    use PluginCommandTrait;

    protected string $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:make-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new notification class for the specified plugin.';

    public function getDefaultNamespace(): string
    {
        $repository = $this->laravel['plugins.repository'];

        return $repository->config('paths.generator.notifications.namespace') ?: $repository->config('paths.generator.notifications.path', 'Notifications');
    }

    /**
     * Get template contents.
     *
     * @return string
     */
    protected function getTemplateContents(): string
    {
        $plugin = $this->getPlugin();

        return (new Stub('/notification.stub', [
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

        $notificationPath = GenerateConfigReader::read('notifications');

        return $path . $notificationPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name'));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the notification class.'],
            ['plugin', InputArgument::OPTIONAL, 'The name of plugin will be used.'],
        ];
    }
}
