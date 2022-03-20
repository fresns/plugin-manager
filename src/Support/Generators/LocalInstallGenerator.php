<?php

namespace Fresns\PluginManager\Support\Generators;

use Illuminate\Console\Command as Console;
use Illuminate\Filesystem\Filesystem;
use Fresns\PluginManager\Contracts\ActivatorInterface;
use Fresns\PluginManager\Contracts\GeneratorInterface;
use Fresns\PluginManager\Exceptions\LocalPathNotFoundException;
use Fresns\PluginManager\Exceptions\PluginAlreadyExistException;
use Fresns\PluginManager\Support\DecompressPlugin;
use Fresns\PluginManager\Support\Json;
use Fresns\PluginManager\Support\Repositories\FileRepository;

class LocalInstallGenerator implements GeneratorInterface
{
    /**
     * The plugin name will created.
     *
     * @var string
     */
    protected string $localPath;

    /**
     * The laravel filesystem instance.
     *
     * @var Filesystem|null
     */
    protected ?Filesystem $filesystem;

    /**
     * The laravel console instance.
     *
     * @var Console|null
     */
    protected ?Console $console;

    /**
     * The activator instance.
     *
     * @var ActivatorInterface|null
     */
    protected ?ActivatorInterface $activator;

    /**
     * The plugin instance.
     *
     * @var FileRepository|null
     */
    protected ?FileRepository $pluginRepository;

    /**
     * Force status.
     *
     * @var bool
     */
    protected bool $force = false;

    /**
     * Enables the plugin.
     *
     * @var bool
     */
    protected bool $isActive = false;

    public static function make(): LocalInstallGenerator
    {
        return new LocalInstallGenerator();
    }

    public function setLocalPath(string $localPath): LocalInstallGenerator
    {
        $this->localPath = $localPath;

        return $this;
    }

    public function setPluginRepository(FileRepository $pluginRepository): LocalInstallGenerator
    {
        $this->pluginRepository = $pluginRepository;

        return $this;
    }

    public function setActivator(ActivatorInterface $activator): LocalInstallGenerator
    {
        $this->activator = $activator;

        return $this;
    }

    public function setFilesystem(Filesystem $filesystem): LocalInstallGenerator
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    public function setConsole(Console $console): LocalInstallGenerator
    {
        $this->console = $console;

        return $this;
    }

    public function setActive(bool $active): LocalInstallGenerator
    {
        $this->isActive = $active;

        return $this;
    }

    public function generate(): int
    {
        if ($this->filesystem->isDirectory($this->localPath)) {
            if (!$this->filesystem->exists("{$this->localPath}/plugin.json")) {
                throw new LocalPathNotFoundException("Local Path [{$this->localPath}] does not exist!");
            }

            $pluginName = Json::make("{$this->localPath}/plugin.json")->get('unikey');

            if ($this->pluginRepository->has($pluginName)) {
                throw new PluginAlreadyExistException("Plugin [{$pluginName}] already exists!");
            }

            $buildPluginPath = $this->pluginRepository->getPluginPath($pluginName);

            if (!$this->filesystem->isDirectory($buildPluginPath)) {
                $this->filesystem->makeDirectory($buildPluginPath, 0775, true);
            }

            $this->filesystem->copyDirectory(
                $this->localPath,
                $buildPluginPath
            );
        } elseif ($this->filesystem->isFile($this->localPath) && $this->filesystem->extension($this->localPath) === 'zip') {
            $pluginName = (new DecompressPlugin($this->localPath))->handle();
        } else {
            // The path passed is not a zip archive, nor is the directory unresolved to the plugin name
            throw new \RuntimeException("Local Path [{$this->localPath}] does not support to unzip!");
        }

        $this->activator->setActiveByName($pluginName, $this->isActive);

        $this->console->info("Plugin [{$pluginName}] created successfully.");

        $plugin = $this->pluginRepository->findOrFail($pluginName);

        $plugin->fireInstalledEvent();

        return 0;
    }
}
