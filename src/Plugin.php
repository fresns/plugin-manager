<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager;

use Fresns\PluginManager\Manager\FileManager;
use Fresns\PluginManager\Support\Json;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Plugin
{
    protected $pluginName;

    /**
     * @var FileManager
     */
    protected $manager;

    public function __construct(?string $pluginName = null)
    {
        $this->manager = new FileManager();

        $this->setPluginName($pluginName);
    }

    public function config(string $key, $default = null)
    {
        return config('plugins.'.$key, $default);
    }

    public function setPluginName(?string $pluginName = null)
    {
        $this->pluginName = $pluginName;
    }

    public function getUnikey()
    {
        return $this->getStudlyName();
    }

    public function getLowerName(): string
    {
        return Str::lower($this->pluginName);
    }

    public function getStudlyName()
    {
        return Str::studly($this->pluginName);
    }

    public function getKebabName()
    {
        return Str::kebab($this->pluginName);
    }

    public function getSnakeName()
    {
        return Str::snake($this->pluginName);
    }

    public function getClassNamespace()
    {
        $namespace = $this->config('namespace');
        $namespace .= '\\'.$this->getStudlyName();
        $namespace = str_replace('/', '\\', $namespace);

        return trim($namespace, '\\');
    }

    public function getSeederNamespace(): ?string
    {
        return "{$this->getClassNamespace()}\\Database\Seeders\\";
    }

    public function getPluginPath(): ?string
    {
        $path = $this->config('paths.plugins');
        $pluginName = $this->getStudlyName();

        return "{$path}/{$pluginName}";
    }

    public function getFactoryPath()
    {
        $path = $this->getPluginPath();

        return "{$path}/database/factories";
    }

    public function getMigratePath()
    {
        $path = $this->getPluginPath();

        return "{$path}/database/migrations";
    }

    public function getSeederPath(): ?string
    {
        $path = $this->getPluginPath();

        return "{$path}/database/seeders";
    }

    public function getAssetsPath(): ?string
    {
        if (! $this->exists()) {
            return null;
        }

        $path = $this->config('paths.assets');
        $pluginName = $this->getStudlyName();

        return "{$path}/{$pluginName}";
    }

    public function getAssetsSourcePath(): ?string
    {
        if (! $this->exists()) {
            return null;
        }

        $path = $this->getPluginPath();

        return "{$path}/resources/assets";
    }

    public function getComposerJsonPath(): ?string
    {
        $path = $this->getPluginPath();

        return "{$path}/composer.json";
    }

    public function getPluginJsonPath(): ?string
    {
        $path = $this->getPluginPath();

        return "{$path}/plugin.json";
    }

    public function install()
    {
        return $this->manager->install($this->getStudlyName());
    }

    public function activate(): bool
    {
        if (! $this->exists()) {
            return false;
        }

        return $this->manager->activate($this->getStudlyName());
    }

    public function deactivate(): bool
    {
        if (! $this->exists()) {
            return false;
        }

        return $this->manager->deactivate($this->getStudlyName());
    }

    public function uninstall()
    {
        return $this->manager->uninstall($this->getStudlyName());
    }

    public function isActivate(): bool
    {
        if (! $this->exists()) {
            return false;
        }

        return $this->manager->isActivate($this->getStudlyName());
    }

    public function isDeactivate(): bool
    {
        return ! $this->isActivate();
    }

    public function exists(): bool
    {
        if (! $pluginName = $this->getStudlyName()) {
            return false;
        }

        if (in_array($pluginName, $this->all())) {
            return true;
        }

        return false;
    }

    public function all(): array
    {
        $path = $this->config('paths.plugins');
        $pluginJsons = File::glob("$path/**/plugin.json");

        $plugins = [];
        foreach ($pluginJsons as $pluginJson) {
            $pluginName = basename(dirname($pluginJson));

            if (! $this->isValidPlugin($pluginName)) {
                continue;
            }

            if (! $this->isAvailablePlugin($pluginName)) {
                continue;
            }

            $plugins[] = $pluginName;
        }

        return $plugins;
    }

    public function isValidPlugin(?string $pluginName = null)
    {
        if (! $pluginName) {
            $pluginName = $this->getStudlyName();
        }

        if (! $pluginName) {
            return false;
        }

        $path = $this->config('paths.plugins');

        $pluginJsonPath = sprintf('%s/%s/plugin.json', $path, $pluginName);

        $pluginJson = Json::make($pluginJsonPath);

        return $pluginName == $pluginJson->get('unikey');
    }

    public function isAvailablePlugin(?string $pluginName = null)
    {
        if (! $pluginName) {
            $pluginName = $this->getStudlyName();
        }

        if (! $pluginName) {
            return false;
        }

        try {
            // Verify that the program is loaded correctly by loading the program
            $plugin = new Plugin($pluginName);
            $plugin->registerProviders();
        } catch (\Throwable $e) {
            \info("{$pluginName} registration failed, not a valid plugin: ".$e->getMessage());

            return false;
        }

        return true;
    }

    public function allActivate(): array
    {
        return array_keys(array_filter($this->manager->all()));
    }

    public function allDeactivate(): array
    {
        return array_diff($this->all(), $this->allActivate());
    }

    public function registerProviders()
    {
        $providers = Json::make($this->getComposerJsonPath())->get('extra.laravel.providers', []);

        (new \Illuminate\Foundation\ProviderRepository(app(), app('files'), $this->getCachedServicesPath()))
            ->load($providers);
    }

    public function registerAliases(): void
    {
        $aliases = Json::make($this->getComposerJsonPath())->get('extra.laravel.aliases', []);

        $loader = AliasLoader::getInstance();
        foreach ($aliases as $aliasName => $aliasClass) {
            $loader->alias($aliasName, $aliasClass);
        }
    }

    public function getCachedServicesPath(): string
    {
        // This checks if we are running on a Laravel Vapor managed instance
        // and sets the path to a writable one (services path is not on a writable storage in Vapor).
        if (! is_null(env('VAPOR_MAINTENANCE_MODE', null))) {
            return Str::replaceLast('config.php', $this->getSnakeName().'_plugin.php', app()->getCachedConfigPath());
        }

        return Str::replaceLast('services.php', $this->getSnakeName().'_plugin.php', app()->getCachedServicesPath());
    }

    public function manualAddNamespace()
    {
        $unikey = $this->getStudlyName();
        if (! $unikey) {
            return;
        }

        if (file_exists(base_path('/vendor/autoload.php'))) {
            /** @var \Composer\Autoload\ClassLoader $loader */
            $loader = require base_path('/vendor/autoload.php');

            $namespaces = config('plugins.namespaces', []);

            foreach ($namespaces as $namespace => $paths) {
                    return "{$path}/{$unikey}/app";
                }, $paths);
                $loader->addPsr4("{$namespace}\\{$unikey}\\", $appPaths, true);

                $factoryPaths = array_map(function ($path) use ($unikey) {
                    return "{$path}/{$unikey}/database/factories";
                }, $paths);
                $loader->addPsr4("{$namespace}\\{$unikey}\\Database\\Factories\\", $factoryPaths, true);

                $seederPaths = array_map(function ($path) use ($unikey) {
                    return "{$path}/{$unikey}/database/seeders";
                }, $paths);
                $loader->addPsr4("{$namespace}\\{$unikey}\\Database\\Seeders\\", $seederPaths, true);

                $testPaths = array_map(function ($path) use ($unikey) {
                    return "{$path}/{$unikey}/tests";
                }, $paths);
                $loader->addPsr4("{$namespace}\\{$unikey}\\Tests\\", $testPaths, true);
            }
        }
    }

    public function getPluginInfo()
    {
        // Validation: Does the directory name and unikey match correctly
        // Available: Whether the service provider is registered successfully
        $item['Plugin Name'] = "<info>{$this->getStudlyName()}</info>";
        $item['Validation'] = $this->isValidPlugin() ? '<info>true</info>' : '<fg=red>false</fg=red>';
        $item['Available'] = $this->isAvailablePlugin() ? '<info>Available</info>' : '<fg=red>Unavailable</fg=red>';
        $item['Plugin Status'] = $this->isActivate() ? '<info>Activate</info>' : '<fg=red>Deactivate</fg=red>';
        $item['Assets Status'] = file_exists($this->getAssetsPath()) ? '<info>Published</info>' : '<fg=red>Unpublished</fg=red>';
        $item['Plugin Path'] = $this->replaceDir($this->getPluginPath());
        $item['Assets Path'] = $this->replaceDir($this->getAssetsPath());

        return $item;
    }

    public function replaceDir(?string $path)
    {
        if (! $path) {
            return null;
        }

        return ltrim(str_replace(base_path(), '', $path), '/');
    }

    public function __toString()
    {
        return $this->getStudlyName();
    }
}
