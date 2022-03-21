<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Support;

use Fresns\PluginManager\Contracts\ActivatorInterface;
use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\ValueObjects\ValRequires;
use Illuminate\Cache\CacheManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\ProviderRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Translation\Translator;

class Plugin
{
    use Macroable;

    /**
     * The laravel|lumen application instance.
     *
     * @var Application;
     */
    protected $app;

    /**
     * The plugin name.
     *
     * @var string
     */
    protected string $name;

    /**
     * The plugin path.
     *
     * @var string
     */
    protected ?string $path;

    /**
     * Plugin type.
     *
     * @var int
     */
    protected int $type = PluginConstant::PLUGIN_TYPE_EXTENSION;

    protected bool $force = false;

    protected bool $disable = false;

    protected bool $cleardata = false;

    /**
     * @var array of cached Json objects, keyed by filename
     */
    protected array $pluginJson = [];

    /**
     * @var CacheManager
     */
    private CacheManager $cache;

    /**
     * @var Filesystem
     */
    private Filesystem $files;

    /**
     * @var Translator
     */
    private Translator $translator;

    /**
     * @var ActivatorInterface
     */
    private ActivatorInterface $activator;

    /**
     * @var RepositoryInterface
     */
    private RepositoryInterface $repository;

    /**
     * Plugin constructor.
     *
     * @param  Application  $app
     * @param  string  $name
     * @param  string  $path
     */
    public function __construct(Application $app, string $name, ?string $path = '', int $type = PluginConstant::PLUGIN_TYPE_EXTENSION)
    {
        $this->name = $name;
        $this->type = $type;
        $this->path = $path;

        $this->app = $app;
        $this->cache = $app['cache'];
        $this->files = $app['files'];
        $this->translator = $app['translator'];
        $this->activator = $app[ActivatorInterface::class];
        $this->repository = $app[RepositoryInterface::class];
    }

    /**
     * Register the Plugin.
     */
    public function register(): void
    {
        $this->registerAliases();

        $this->registerProviders();

        $this->fireEvent('register');
    }

    /**
     * Bootstrap the application events.
     */
    public function boot(): void
    {
        if (config('plugins.register.translations', true) === true) {
            $this->registerTranslation();
        }

        $this->fireEvent('boot');
    }

    public function getType()
    {
        return $this->type;
    }

    public function setForce(bool $force = false)
    {
        $this->force = $force;

        return $this;
    }

    public function getForce()
    {
        return $this->force;
    }

    public function setDisable(bool $disable = false)
    {
        $this->disable = $disable;

        return $this;
    }

    public function getDisable()
    {
        return $this->disable;
    }

    public function setClearData(bool $cleardata = false)
    {
        $this->cleardata = $cleardata;

        return $this;
    }

    public function getClearData()
    {
        return $this->cleardata;
    }

    /**
     * @return string
     */
    public function getCachedServicesPath(): string
    {
        // This checks if we are running on a Laravel Vapor managed instance
        // and sets the path to a writable one (services path is not on a writable storage in Vapor).
        if (!is_null(env('VAPOR_MAINTENANCE_MODE', null))) {
            return Str::replaceLast('config.php', $this->getSnakeName().'_plugin.php', $this->app->getCachedConfigPath());
        }

        return Str::replaceLast('services.php', $this->getSnakeName().'_plugin.php', $this->app->getCachedServicesPath());
    }

    public function registerProviders(): void
    {
        (new ProviderRepository($this->app, new Filesystem(), $this->getCachedServicesPath()))
            ->load($this->get('providers', []));
    }

    public function registerAliases(): void
    {
        $loader = AliasLoader::getInstance();
        foreach ($this->get('aliases', []) as $aliasName => $aliasClass) {
            $loader->alias($aliasName, $aliasClass);
        }
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get name in kebab case.
     *
     * @return string
     */
    public function getKebabName(): string
    {
        return Str::kebab($this->name);
    }

    /**
     * Get name in studly case.
     *
     * @return string
     */
    public function getStudlyName(): string
    {
        return Str::studly($this->name);
    }

    /**
     * Get name in snake case.
     *
     * @return string
     */
    public function getSnakeName(): string
    {
        return Str::snake($this->name);
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->get('description');
    }

    /**
     * Get alias.
     *
     * @return string
     */
    public function getAlias(): string
    {
        return $this->get('alias');
    }

    /**
     * Get priority.
     *
     * @return string
     */
    public function getPriority(): string
    {
        return $this->get('priority');
    }

    /**
     * Get plugin requirements.
     *
     * @return array
     */
    public function getRequires(): array
    {
        return $this->get('requires');
    }

    /**
     * Get path.
     *
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Set path.
     *
     * @param  string  $path
     * @return Plugin
     */
    public function setPath(string $path): Plugin
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Register plugin's translation.
     *
     * @return void
     */
    protected function registerTranslation(): void
    {
        $kebabName = $this->getKebabName();

        $langPath = $this->getPath().'/Resources/lang';

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $kebabName);
        }
    }

    /**
     * Get json contents from the cache, setting as needed.
     *
     * @param  string|null  $file
     * @return Json
     */
    public function json(?string $file = null): Json
    {
        if ($file === null) {
            $file = match ($this->type) {
                PluginConstant::PLUGIN_TYPE_THEME => 'theme.json',
                default => 'plugin.json',
            };
        }

        return Arr::get($this->pluginJson, $file, function () use ($file) {
            return $this->pluginJson[$file] = new Json($this->getPath().'/'.$file, $this->files);
        });
    }

    /**
     * Get a specific data from json file by given the key.
     *
     * @param  string  $key
     * @param  null  $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->json()->get($key, $default);
    }

    /**
     * @param $key
     * @param  array  $default
     * @return ValRequires
     */
    public function getComposerAttr(string $key, $default = []): ValRequires
    {
        return ValRequires::toValRequires(data_get($this->json()->get('composer'), $key, $default));
    }

    /**
     * @return ValRequires
     */
    public function getAllComposerRequires(): ValRequires
    {
        $composer = $this->json()->get('composer');

        return ValRequires::toValRequires(data_get($composer, 'require', []))->merge(ValRequires::toValRequires(data_get($composer, 'require-dev', [])));
    }

    /**
     * @return Filesystem
     */
    public function getFiles(): Filesystem
    {
        return $this->files;
    }

    /**
     * Register the Plugin event.
     *
     * @param  string  $event
     */
    protected function fireEvent($event): void
    {
        $this->app['events']->dispatch('plugins.'.$event, [$this]);
    }

    public function fireInstallingEvent(): void
    {
        $this->fireEvent('installing');
    }

    public function fireInstalledEvent(): void
    {
        $this->fireEvent('installed');
    }

    /**
     * Handle call __toString.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getStudlyName();
    }

    /**
     * Determine whether the given status same with the current plugin status.
     *
     * @param  bool  $status
     * @return bool
     */
    public function isStatus(bool $status): bool
    {
        return $this->activator->hasStatus($this, $status);
    }

    /**
     * Determine whether the current plugin activated.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->activator->hasStatus($this, true);
    }

    /**
     *  Determine whether the current plugin not disabled.
     *
     * @return bool
     */
    public function isDisabled(): bool
    {
        return !$this->isEnabled();
    }

    /**
     * Set active state for current plugin.
     *
     * @param  bool  $active
     * @return void
     */
    public function setActive(bool $active): void
    {
        $this->activator->setActive($this, $active);
    }

    public function getCompressFilePath(): string
    {
        return $this->getPath().'/.compress/'.$this->getName().'.zip';
    }

    public function getCompressDirectoryPath(): string
    {
        return $this->getPath().'/.compress/';
    }

    /**
     * Deactivate the current plugin.
     */
    public function deactivate(): void
    {
        $this->fireEvent('deactivating');

        $this->activator->disable($this);
        $this->flushCache();

        $this->fireEvent('deactivated');
    }

    /**
     * Activate the current plugin.
     */
    public function activate(): void
    {
        $this->fireEvent('activating');

        $this->activator->enable($this);
        $this->flushCache();

        $this->fireEvent('activated');
    }

    public function fireEventClearData()
    {
        $this->fireEvent('cleardata');
    }

    /**
     * Delete the current plugin.
     *
     * @return bool
     */
    public function delete(): bool
    {
        if ($this->getClearData()) {
            $this->fireEventClearData();
        }

        $this->fireEvent('uninstalling');

        $this->activator->delete($this);

        // Delete cache files
        $this->files->delete(base_path(sprintf('bootstrap/cache/%s_plugin.php', $this->getSnakeName())));

        // Delete the static resource directory under public
        $this->files->deleteDirectory(public_path(sprintf('assets/%s', $this->getName())));

        $res = $this->files->deleteDirectory($this->getPath());

        $this->fireEvent('uninstalled');

        return $res;
    }

    /**
     * Get extra path.
     *
     * @param  string  $path
     * @return string
     */
    public function getExtraPath(string $path): string
    {
        return $this->getPath().'/'.$path;
    }

    /**
     * Check if can load files of plugin on boot method.
     */
    protected function isLoadFilesOnBoot(): bool
    {
        return false;
    }

    private function flushCache(): void
    {
        if (config('plugins.cache.enabled')) {
            $this->cache->store()->flush();
        }
    }

    /**
     * Register a translation file namespace.
     *
     * @param  string  $path
     * @param  string  $namespace
     * @return void
     */
    private function loadTranslationsFrom(string $path, string $namespace): void
    {
        $this->translator->addNamespace($namespace, $path);
    }
}
