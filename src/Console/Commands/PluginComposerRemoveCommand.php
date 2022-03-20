<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Support\Composer\ComposerRemove;
use Fresns\PluginManager\Traits\PluginCommandTrait;
use Fresns\PluginManager\ValueObjects\ValRequire;
use Fresns\PluginManager\ValueObjects\ValRequires;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Input\InputArgument;

class PluginComposerRemoveCommand extends Command
{
    use PluginCommandTrait;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:composer-remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the plugin composer package.';

    public function handle(): void
    {
        $packages = $this->argument('packages');
        $plugin = $this->argument('plugin');
        $pluginJson = $this->getPlugin()->json();

        try {
            $vrs = ValRequires::make();
            foreach ($packages as $package) {
                $vrs->append(ValRequire::make($package));
            }
            ComposerRemove::make()->appendRemovePluginRequires($plugin, $vrs)->run();
            $composer = $pluginJson->get('composer');

            foreach ($packages as $package) {
                Arr::forget($composer, "require.$package");
                Arr::forget($composer, "require-dev.$package");
            }

            $pluginJson->set('composer', $composer)->save();
            $this->info("Package {$vrs}remove complete.");
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    protected function getArguments(): array
    {
        return [
            ['plugin', InputArgument::REQUIRED, 'The name of plugins will be used.'],
            ['packages', InputArgument::IS_ARRAY, 'The name of the composer package name.'],
        ];
    }
}
