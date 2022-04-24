<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\Support\Config\GenerateConfigReader;
use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Traits\PluginCommandTrait;
use Illuminate\Console\Command;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PluginSeedCommand extends Command
{
    use PluginCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the given plugin, or without an argument, seed all plugins.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            if ($name = $this->argument('plugin')) {
                $name = Str::studly($name);
                $this->PluginSeed($this->getPluginByName($name));
            } else {
                $plugins = $this->getPluginRepository()->getOrdered();
                array_walk($plugins, [$this, 'pluginSeed']);
                $this->info('All Plugins seeded.');
            }
        } catch (\Error $e) {
            $e = new \ErrorException($e->getMessage(), $e->getCode(), 1, $e->getFile(), $e->getLine(), $e);
            $this->reportException($e);
            $this->renderException($this->getOutput(), $e);

            return E_ERROR;
        } catch (\Exception $e) {
            $this->reportException($e);
            $this->renderException($this->getOutput(), $e);

            return E_ERROR;
        }

        return 0;
    }

    /**
     * @return RepositoryInterface
     *
     * @throws \RuntimeException
     */
    public function getPluginRepository(): RepositoryInterface
    {
        $plugins = $this->laravel['plugins.repository'];
        if (! $plugins instanceof RepositoryInterface) {
            throw new \RuntimeException('plugin repository not found!');
        }

        return $plugins;
    }

    /**
     * @param $name
     * @return Plugin
     *
     * @throws \RuntimeException
     */
    public function getPluginByName($name)
    {
        $plugins = $this->getPluginRepository();
        if ($plugins->has($name) === false) {
            throw new \RuntimeException("Plugin [$name] does not exists.");
        }

        return $plugins->find($name);
    }

    /**
     * @param  Plugin  $plugin
     * @return void
     */
    public function pluginSeed(Plugin $plugin)
    {
        $seeders = [];
        $name = $plugin->getName();
        $config = $plugin->get('migration');
        if (is_array($config) && array_key_exists('seeds', $config)) {
            foreach ((array) $config['seeds'] as $class) {
                if (class_exists($class)) {
                    $seeders[] = $class;
                }
            }
        } else {
            $class = $this->getSeederName($name); //legacy support
            if (class_exists($class)) {
                $seeders[] = $class;
            } else {
                //look at other namespaces
                $classes = $this->getSeederNames($name);
                foreach ($classes as $class) {
                    if (class_exists($class)) {
                        $seeders[] = $class;
                    }
                }
            }
        }

        if (count($seeders) > 0) {
            array_walk($seeders, [$this, 'dbSeed']);
            $this->info("Plugin [$name] seeded.");
        }
    }

    /**
     * Seed the specified Plugin.
     *
     * @param  string  $className
     */
    protected function dbSeed($className)
    {
        if ($option = $this->option('class')) {
            $params['--class'] = Str::finish(substr($className, 0, strrpos($className, '\\')), '\\').$option;
        } else {
            $params = ['--class' => $className];
        }

        if ($option = $this->option('database')) {
            $params['--database'] = $option;
        }

        if ($option = $this->option('force')) {
            $params['--force'] = $option;
        }

        $this->call('db:seed', $params);
    }

    /**
     * Get master database seeder name for the specified Plugin.
     *
     * @param  string  $name
     * @return string
     */
    public function getSeederName($name)
    {
        $name = Str::studly($name);

        $namespace = $this->laravel['plugins.repository']->config('namespace');
        $config = GenerateConfigReader::read('seeder');
        $seederPath = str_replace('/', '\\', $config->getPath());

        return $namespace.'\\'.$name.'\\'.$seederPath.'\\'.$name.'DatabaseSeeder';
    }

    /**
     * Get master database seeder name for the specified Plugin under a different namespace than Plugins.
     *
     * @param  string  $name
     * @return array $foundPlugins array containing namespace paths
     */
    public function getSeederNames($name)
    {
        $name = Str::studly($name);

        $seederPath = GenerateConfigReader::read('seeder');
        $seederPath = str_replace('/', '\\', $seederPath->getPath());

        $foundPlugins = [];
        foreach ($this->laravel['plugins.repository']->allEnabled() as $plugin) {
            if ($plugin->getName() !== $name) {
                continue;
            }

            $namespace = array_slice(explode('/', $plugin->getPath()), -1)[0];

            $foundPlugins[] = '\\Plugins\\' . $namespace.'\\'.$seederPath.'\\'.$name.'DatabaseSeeder';
        }

        return $foundPlugins;
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @param  \Throwable  $e
     * @return void
     */
    protected function renderException($output, \Exception $e)
    {
        $this->laravel[ExceptionHandler::class]->renderForConsole($output, $e);
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param  \Throwable  $e
     * @return void
     */
    protected function reportException(\Exception $e)
    {
        $this->laravel[ExceptionHandler::class]->report($e);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['plugin', InputArgument::OPTIONAL, 'The name of plugin will be used.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['class', null, InputOption::VALUE_OPTIONAL, 'The class name of the root seeder.'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to seed.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
        ];
    }
}
