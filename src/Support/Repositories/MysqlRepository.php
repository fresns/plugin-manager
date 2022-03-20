<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Support\Repositories;

use Fresns\PluginManager\Enums\PluginStatus;
use Fresns\PluginManager\Exceptions\PluginNotFoundException;
use Fresns\PluginManager\Models\InstallPlugin;
use Fresns\PluginManager\Support\Plugin;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

class MysqlRepository
{
    use Macroable;

    /**
     * @var Application
     */
    protected Application $app;

    /**
     * @var CacheManager
     */
    private CacheManager $cache;

    /**
     * @var ConfigRepository
     */
    private ConfigRepository $config;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->config = $app['config'];
        $this->cache = $app['cache'];
    }

    /**
     * @param  mixed  ...$args
     * @return Plugin
     */
    protected function createPlugin(...$args): Plugin
    {
        return new Plugin(...$args);
    }

    /**
     * @return Builder
     */
    public function query(): Builder
    {
        return InstallPlugin::query();
    }

    /**
     * Get all plugins.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        if (! $this->config('cache.enabled')) {
            return $this->scan();
        }

        return $this->getCached();
    }

    /**
     * Get cached plugins.
     *
     * @return Collection
     */
    public function getCached(): Collection
    {
        return $this->cache->remember($this->config('cache.key'), $this->config('cache.lifetime'), function () {
            return $this->scan();
        });
    }

    /**
     * Scan & get all available plugins.
     *
     * @return Collection
     */
    public function scan(): Collection
    {
        return $this->query()->get();
    }

    /**
     * Get plugin as plugins collection instance.
     *
     * @return Collection
     */
    public function toCollection(): Collection
    {
        return new Collection([]);
    }

    /**
     * Get scanned paths.
     *
     * @return array
     */
    public function getScanPaths(): array
    {
        return [];
    }

    /**
     * Get list of enabled plugins.
     *
     * @return Collection
     */
    public function allEnabled(): Collection
    {
        return $this->getByStatus(PluginStatus::enable());
    }

    /**
     * Get list of disabled plugins.
     *
     * @return mixed
     */
    public function allDisabled()
    {
    }

    /**
     * Get count from all plugins.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->all()->count();
    }

    /**
     * Get all ordered plugins.
     *
     * @param  string  $direction
     * @return mixed
     */
    public function getOrdered($direction = 'asc')
    {
    }

    /**
     * Get plugins by the given status.
     *
     * @param  PluginStatus  $status
     * @return Collection
     */
    public function getByStatus(PluginStatus $status): Collection
    {
        return $this->all()->filter(fn (InstallPlugin $plugin) => $status->equals($plugin->status));
    }

    /**
     * Find a specific plugin.
     *
     * @param  string  $name
     * @return InstallPlugin|null
     */
    public function find(string $name): ?InstallPlugin
    {
        return $this->all()->first(fn (InstallPlugin $plugin) => $plugin->kebab_name === Str::kebab($name));
    }

    /**
     * Find all plugins that are required by a plugin. If the plugin cannot be found, throw an exception.
     *
     * @param  string  $name
     * @return array
     */
    public function findRequirements(string $name): array
    {
        return [];
    }

    /**
     * Find a specific plugin. If there return that, otherwise throw exception.
     *
     * @param  string  $name
     * @return InstallPlugin
     *
     * @throws PluginNotFoundException
     */
    public function findOrFail(string $name): InstallPlugin
    {
        $plugin = $this->all()->first(fn (InstallPlugin $plugin) => $plugin->kebab_name === Str::kebab($name));

        if ($plugin !== null) {
            return $plugin;
        }

        throw new PluginNotFoundException("Plugin [{$name}] does not exist!");
    }

    /**
     * @param  string  $pluginName
     * @return string
     */
    public function getPluginPath(string $pluginName): string
    {
        return '';
    }

    /**
     * @return Filesystem
     */
    public function getFiles(): Filesystem
    {
        return new Filesystem();
    }

    /**
     * Get a specific config data from a configuration file.
     *
     * @param  string  $key
     * @param  string|null  $default
     * @return mixed
     */
    public function config(string $key, $default = null)
    {
        return $this->config->get('plugins.'.$key, $default);
    }

    /**
     * Get a plugin path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->config('paths.plugins', base_path('plugins'));
    }

    /**
     * Find a specific plugin by its alias.
     *
     * @param  string  $alias
     * @return InstallPlugin|null
     */
    public function findByAlias(string $alias): ?InstallPlugin
    {
        return $this->all()->first(fn (InstallPlugin $plugin) => Str::kebab($plugin->alias) === Str::kebab($alias));
    }

    /**
     * Boot the plugins.
     */
    public function boot(): void
    {
    }

    /**
     * Register the plugins.
     */
    public function register(): void
    {
    }

    /**
     * Get asset path for a specific plugin.
     *
     * @param  string  $name
     * @return string
     */
    public function assetPath(string $name): string
    {
        return '';
    }

    /**
     * Delete a specific plugin.
     *
     * @param  string  $name
     * @return bool
     *
     * @throws PluginNotFoundException
     */
    public function delete(string $name): bool
    {
        return true;
    }

    /**
     * Determine whether the given plugin is activated.
     *
     * @param  string  $name
     * @return bool
     *
     * @throws PluginNotFoundException
     */
    public function isEnabled(string $name): bool
    {
        return true;
    }

    /**
     * Determine whether the given plugin is not activated.
     *
     * @param  string  $name
     * @return bool
     *
     * @throws PluginNotFoundException
     */
    public function isDisabled(string $name): bool
    {
        return true;
    }
}
