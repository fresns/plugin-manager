<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Support\PluginConstant;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PluginInstallCommand extends Command
{
    protected $name = 'plugin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the plugin for the specified file directory or zip file.';

    /**
     * @return int
     */
    public function handle(): int
    {
        /** @var \Illuminate\Filesystem\Filesystem $fileSystem */
        $this->fileSystem = app('files');

        try {
            $this->unzipPlugin();

            $pluginName = $this->argument('plugin') ?? cache()->pull(PluginUnzipCommand::CACHE_INSTALL_PLUGIN_NAME);

            $plugin = new Plugin($this->getLaravel(), $pluginName, $this->argument('path'), $this->option('type'));

            $oldStatus = $this->getStatus($plugin);

            // force flag and disable flag need pass to oather event listener.
            $plugin->setForce($this->option('force'));
            $plugin->setDisable($this->option('disabled'));

            // trigger install flow

            // see config: event.installing
            $plugin->fireInstallingEvent();

            // see config: event.installed
            $plugin->fireInstalledEvent();

            if ($oldStatus) {
                $plugin->activate();
            } else {
                $plugin->deactivate();
            }

            $this->info("{$plugin->getName()} install successful");

            return 0;
            // return $code;
        } catch (\Exception $exception) {
            info('plugin:install fail', [
                'message' => $exception->getMessage(),
            ]);
            $this->error($exception->getMessage());

            return E_ERROR;
        }
    }

    public function unzipPlugin()
    {
        $command = 'plugin:unzip';
        if ($this->option('type') === \Fresns\PluginManager\Support\PluginConstant::PLUGIN_TYPE_THEME) {
            $command = 'theme:unzip';
        }

        \Artisan::call($command, [
            'path' => $this->argument('path'),
        ]);
    }

    public function getStatus($plugin)
    {
        return $plugin->isEnabled();
    }

    protected function getArguments(): array
    {
        return [
            ['path', InputArgument::REQUIRED, 'Local path.'],
            ['plugin', InputArgument::OPTIONAL, 'Plugin name.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['type', null, InputOption::VALUE_OPTIONAL, 'This plugin type.', PluginConstant::PLUGIN_TYPE_EXTENSION],
            ['disabled', 'd', InputOption::VALUE_NONE, 'Do not enable the plugin at creation.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when the plugin already exists.'],
        ];
    }
}
