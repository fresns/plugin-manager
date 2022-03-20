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

class ThemeInstallCommand extends Command
{
    protected $name = 'theme:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the theme through the file directory.';

    /**
     * @return int
     */
    public function handle(): int
    {
        try {
            $plugin = new Plugin($this->getLaravel(), $this->argument('theme'), $this->argument('path'), PluginConstant::PLUGIN_TYPE_THEME);

            // force flag and disable flag need pass to oather event listener.
            $plugin->setForce($this->option('force'));
            $plugin->setDisable($this->option('disabled'));

            // trigger install flow

            // see config: event.installing
            $plugin->fireInstallingEvent();

            // see config: event.installed
            $plugin->fireInstalledEvent();

            $themeName = $plugin->getName();

            $this->info("Theme $themeName installed.");

            return 0;
            // return $code;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());

            return E_ERROR;
        }
    }

    protected function getArguments(): array
    {
        return [
            ['path', InputArgument::REQUIRED, 'Local path.'],
            ['theme', InputArgument::REQUIRED, 'Theme name.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['disabled', 'd', InputOption::VALUE_NONE, 'Do not enable the theme at creation.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when the theme already exists.'],
        ];
    }
}
