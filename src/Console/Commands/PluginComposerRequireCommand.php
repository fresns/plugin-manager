<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Support\Composer\ComposerRequire;
use Fresns\PluginManager\Support\Json;
use Fresns\PluginManager\Traits\PluginCommandTrait;
use Fresns\PluginManager\ValueObjects\ValRequires;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PluginComposerRequireCommand extends Command
{
    use PluginCommandTrait;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:composer-require';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the plugin composer package.';

    public function handle(): void
    {
        try {
            $plugin = $this->argument('plugin');

            $package = $this->argument('package');

            $pluginJson = $this->getPlugin()->json();

            $require = $this->option('dev') ? 'require-dev' : 'require';

            $vrs = ValRequires::toValRequires([
                $package => $this->option('v'),
            ]);

            $composerRequire = ComposerRequire::make();

            $this->option('dev') ? $composerRequire->appendPluginDevRequires($plugin, $vrs)->run() : $composerRequire->appendPluginRequires($plugin, $vrs)->run();

            $composer = $pluginJson->get('composer', []);
            $version = data_get(Json::make('composer.json')->setIsCache(false)->get($require), $package);
            $composer[$require][$package] = $version;
            $pluginJson->set('composer', $composer)->save();
            $this->info("Package {$vrs}generated successfully.");
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    protected function getArguments(): array
    {
        return [
            ['plugin', InputArgument::REQUIRED, 'The name of plugins will be used.'],
            ['package', InputArgument::REQUIRED, 'The name of the composer package name.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['dev', null, InputOption::VALUE_NONE, 'Only the composer package of the dev environment exists.'],
            ['v', null, InputOption::VALUE_OPTIONAL, 'The version number of the composer package.'],
        ];
    }
}
