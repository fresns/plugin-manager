<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
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
    protected $pluginFskey;

    /**
     * @var FileManager
     */
    protected $manager;

    public function __construct(?string $pluginFskey = null)
    {
        $this->manager = new FileManager();

        $this->setPluginFskey($pluginFskey);
    }

    public function config(string $key, $default = null)
    {
        return config('plugins.'.$key, $default);
    }

    public function setPluginFskey(?string $pluginFskey = null)
    {
        $this->pluginFskey = $pluginFskey;
    }

    public function getFskey()
    {
        return $this->getStudlyName();
    }

    public function getLowerName(): string
    {
        return Str::lower($this->pluginFskey);
    }

    public function getStudlyName()
    {
        return Str::studly($this->pluginFskey);
    }

    public function getKebabName()
    {
        return Str::kebab($this->pluginFskey);
    }

    public function getSnakeName()
    {
        return Str::snake($this->pluginFskey);
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
        $pluginFskey = $this->getStudlyName();

        return "{$path}/{$pluginFskey}";
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
        $pluginFskey = $this->getStudlyName();

        return "{$path}/{$pluginFskey}";
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
        if (! $pluginFskey = $this->getStudlyName()) {
            return false;
        }

        if (in_array($pluginFskey, $this->all())) {
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
            $pluginFskey = basename(dirname($pluginJson));

            if (! $this->isValidPlugin($pluginFskey)) {
                continue;
            }

            if (! $this->isAvailablePlugin($pluginFskey)) {
                continue;
            }

            $plugins[] = $pluginFskey;
        }

        return $plugins;
    }

    public function isValidPlugin(?string $pluginFskey = null)
    {
        if (! $pluginFskey) {
            $pluginFskey = $this->getStudlyName();
        }

        if (! $pluginFskey) {
            return false;
        }

        $path = $this->config('paths.plugins');

        $pluginJsonPath = sprintf('%s/%s/plugin.json', $path, $pluginFskey);

        $pluginJson = Json::make($pluginJsonPath);

        return $pluginFskey == $pluginJson->get('fskey');
    }

    public function isAvailablePlugin(?string $pluginFskey = null)
    {
        if (! $pluginFskey) {
            $pluginFskey = $this->getStudlyName();
        }

        if (! $pluginFskey) {
            return false;
        }

        try {
            // Verify that the program is loaded correctly by loading the program
            $plugin = new Plugin($pluginFskey);
            $plugin->manualAddNamespace();

            $serviceProvider = sprintf('%s\\Providers\\%sServiceProvider', $plugin->getClassNamespace(), $pluginFskey);
            $pluginServiceProvider = sprintf('%s\\Providers\\PluginServiceProvider', $plugin->getClassNamespace(), $pluginFskey);;

            return class_exists($serviceProvider) || class_exists($pluginServiceProvider);
        } catch (\Throwable $e) {
            \info("{$pluginFskey} registration failed, not a valid plugin: ".$e->getMessage());

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

    public function registerFiles()
    {
        $path = $this->getPluginPath();

        $files = Json::make($this->getPluginJsonPath())->get('autoloadFiles', []);
        foreach ($files as $file) {
            if (! is_string($file)) {
                continue;
            }

            $filepath = "$path/$file";
            if (is_file($filepath)) {
                include_once $filepath;
            }
        }
    }

    public function registerProviders()
    {
        $providers = Json::make($this->getPluginJsonPath())->get('providers', []);

        (new \Illuminate\Foundation\ProviderRepository(app(), app('files'), $this->getCachedServicesPath()))
            ->load($providers);
    }

    public function registerAliases(): void
    {
        $aliases = Json::make($this->getPluginJsonPath())->get('aliases', []);

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
        $fskey = $this->getStudlyName();
        if (! $fskey) {
            return;
        }

        if (file_exists(base_path('/vendor/autoload.php'))) {
            /** @var \Composer\Autoload\ClassLoader $loader */
            $loader = require base_path('/vendor/autoload.php');

            $namespaces = config('plugins.namespaces', []);

            foreach ($namespaces as $namespace => $paths) {
                $appPaths = array_map(function ($path) use ($fskey) {
                    return "{$path}/{$fskey}/app";
                }, $paths);
                $loader->addPsr4("{$namespace}\\{$fskey}\\", $appPaths, true);

                $factoryPaths = array_map(function ($path) use ($fskey) {
                    return "{$path}/{$fskey}/database/factories";
                }, $paths);
                $loader->addPsr4("{$namespace}\\{$fskey}\\Database\\Factories\\", $factoryPaths, true);

                $seederPaths = array_map(function ($path) use ($fskey) {
                    return "{$path}/{$fskey}/database/seeders";
                }, $paths);
                $loader->addPsr4("{$namespace}\\{$fskey}\\Database\\Seeders\\", $seederPaths, true);

                $testPaths = array_map(function ($path) use ($fskey) {
                    return "{$path}/{$fskey}/tests";
                }, $paths);
                $loader->addPsr4("{$namespace}\\{$fskey}\\Tests\\", $testPaths, true);
            }
        }
    }

    public function getPluginInfo()
    {
        // Validation: Does the directory name and fskey match correctly
        // Available: Whether the service provider is registered successfully
        $item['Plugin Fskey'] = "<info>{$this->getStudlyName()}</info>";
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
