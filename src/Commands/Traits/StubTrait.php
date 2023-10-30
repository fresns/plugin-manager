<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands\Traits;

use Fresns\PluginManager\Support\Config\GenerateConfigReader;
use Fresns\PluginManager\Support\Json;
use Fresns\PluginManager\Support\Stub;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait StubTrait
{
    protected $runningAsRootDir = false;
    protected $buildClassName = null;

    /**
     * Replace a given string within a given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $path
     */
    protected function replaceInFile($search, $replace, $path): void
    {
        if (! is_file($path)) {
            return;
        }

        $content = file_get_contents($path);
        if (! str_contains($content, $replace)) {
            file_put_contents($path, str_replace($search, $replace, $content));
        }
    }

    public function getPluginJsonReplaceContent($provider, $pluginFskey): string
    {
        $class = sprintf('Plugins\\%s\\Providers\\%s', $pluginFskey, $provider);
        $class = str_replace('\\', '\\\\', $class);

        return $class;
    }

    public function getPluginJsonSearchContent($pluginFskey): string
    {
        $class = sprintf('Plugins\\%s\\Providers\\%sServiceProvider', $pluginFskey, $pluginFskey);
        $class = str_replace('\\', '\\\\', $class);

        return $class;
    }

    /**
     * Install the provider in the plugin.json file.
     *
     * @param  string  $after
     * @param  string  $name
     * @param  string  $group
     */
    protected function installPluginProviderAfter(string $after, string $name, string $pluginJsonPath): void
    {
        $pluginJson = file_get_contents($pluginJsonPath);

        $providers = Str::before(Str::after($pluginJson, '"providers": ['), sprintf('],%s    "autoloadFiles"', PHP_EOL));

        if (! Str::contains($providers, $name)) {
            $modifiedProviders = str_replace(
                sprintf('"%s",', $after),
                sprintf('"%s",', $after).PHP_EOL.'        '.sprintf('"%s",', $name),
                $providers,
            );

            $this->replaceInFile(
                $providers,
                $modifiedProviders,
                $pluginJsonPath,
            );
        }
    }

    protected function getNameInput(): string
    {
        return trim($this->argument('fskey'));
    }

    protected function buildClass($fskey): string
    {
        $this->runningAsRootDir = false;
        if (str_starts_with($fskey, 'App')) {
            $this->runningAsRootDir = true;
            $this->buildClassName = $fskey;
        }

        $content = $this->getStubContents($this->getStub());

        return $content;
    }

    protected function getPath($fskey): mixed
    {
        $path = parent::getPath($fskey);

        $this->type = $path;

        return $path;
    }

    protected function getDefaultNamespace($rootNamespace): mixed
    {
        return $rootNamespace;
    }

    protected function getStubName(): ?string
    {
        return null;
    }

    /**
     * implement from \Illuminate\Console\GeneratorCommand.
     *
     * @see \Illuminate\Console\GeneratorCommand
     */
    protected function getStub(): string
    {
        $stubName = $this->getStubName();
        if (! $stubName) {
            throw new \RuntimeException('Please provider stub fskey in getStubName method');
        }

        $baseStubPath = base_path("stubs/{$stubName}.stub");
        if (file_exists($baseStubPath)) {
            return $baseStubPath;
        }

        $stubPath = dirname(__DIR__)."/stubs/{$stubName}.stub";
        if (file_exists($stubPath)) {
            return $stubPath;
        }

        throw new \RuntimeException("stub path does not exists: {$stubPath}");
    }

    /**
     * Get class name.
     */
    public function getClass(): string
    {
        return class_basename($this->argument('fskey'));
    }

    /**
     * Get the contents of the specified stub file by given stub name.
     *
     * @param  $stub
     */
    protected function getStubContents($stubPath): string
    {
        $method = sprintf('get%sStubPath', Str::studly(strtolower($stubPath)));

        // custom stubPath
        if (method_exists($this, $method)) {
            $stubFilePath = $this->$method();
        } else {
            // run in command: fresns new Xxx
            $stubFilePath = dirname(__DIR__)."/stubs/{$stubPath}.stub";

            if (file_exists($stubFilePath)) {
                $stubFilePath = $stubFilePath;
            }
            // run in command: fresns make:xxx
            else {
                $stubFilePath = $stubPath;
            }
        }

        if (! file_exists($stubFilePath)) {
            throw new \RuntimeException("stub path does not exists: {$stubPath}");
        }

        $mimeType = File::mimeType($stubFilePath);
        if (
            str_contains($mimeType, 'application/')
            || str_contains($mimeType, 'text/')
        ) {
            $stubFile = new Stub($stubFilePath, $this->getReplacement($stubFilePath));
            $content = $stubFile->render();
        } else {
            $content = File::get($stubFilePath);
        }

        // format json style
        if (str_contains($stubPath, 'json')) {
            $content = Json::make()->decode($content)->encode();

            return $content;
        }

        return $content;
    }

    public function getReplaceKeys($content): ?array
    {
        preg_match_all('/(\$[^\s.]*?\$)/', $content, $matches);

        $keys = $matches[1] ?? [];

        return $keys;
    }

    public function getReplacesByKeys(array $keys): ?array
    {
        $replaces = [];
        foreach ($keys as $key) {
            $currentReplacement = str_replace('$', '', $key);

            $currentReplacementLower = Str::of($currentReplacement)->lower()->toString();
            $method = sprintf('get%sReplacement', Str::studly($currentReplacementLower));

            if (method_exists($this, $method)) {
                $replaces[$currentReplacement] = $this->$method();
            } else {
                \info($currentReplacement.' does match any replace content');
                // keep origin content
                $replaces[$currentReplacement] = $key;
            }
        }

        return $replaces;
    }

    public function getReplacedContent(string $content, array $keys = []): string
    {
        if (! $keys) {
            $keys = $this->getReplaceKeys($content);
        }

        $replaces = $this->getReplacesByKeys($keys);

        return str_replace($keys, $replaces, $content);
    }

    /**
     * Get array replacement for the specified stub.
     *
     * @param  $stub
     */
    protected function getReplacement($stubPath): array
    {
        if (! file_exists($stubPath)) {
            throw new \RuntimeException("stubPath $stubPath not exists");
        }

        $stubContent = @file_get_contents($stubPath);

        $keys = $this->getReplaceKeys($stubContent);

        $replaces = $this->getReplacesByKeys($keys);

        return $replaces;
    }

    public function getAuthorsReplacement(): mixed
    {
        return Json::make()->encode(config('plugins.composer.author'));
    }

    public function getAuthorNameReplacement(): mixed
    {
        $authors = config('plugins.composer.author');
        if (count($authors)) {
            return $authors[0]['name'] ?? 'Fresns';
        }

        return 'Fresns';
    }

    public function getAuthorLinkReplacement(): mixed
    {
        $authors = config('plugins.composer.author');
        if (count($authors)) {
            return $authors[0]['homepage'] ?? 'https://fresns.org';
        }

        return 'https://fresns.org';
    }

    /**
     * Get namespace for plugin service provider.
     */
    protected function getNamespaceReplacement(): string
    {
        if ($this->runningAsRootDir) {
            return Str::beforeLast($this->buildClassName, '\\');
        }

        $namespace = $this->plugin->getClassNamespace();
        $namespace = $this->getDefaultNamespace($namespace);

        return str_replace('\\\\', '\\', $namespace);
    }

    public function getClassReplacement(): string
    {
        return $this->getClass();
    }

    /**
     * Get the plugin fskey in lower case.
     */
    protected function getLowerNameReplacement(): string
    {
        return $this->plugin->getLowerName();
    }

    /**
     * Get the plugin fskey in studly case.
     */
    protected function getStudlyNameReplacement(): string
    {
        return $this->plugin->getStudlyName();
    }

    /**
     * Get the plugin fskey in studly case.
     */
    protected function getSnakeNameReplacement(): string
    {
        return $this->plugin->getSnakeName();
    }

    /**
     * Get the plugin fskey in kebab case.
     */
    protected function getKebabNameReplacement(): string
    {
        return $this->plugin->getKebabName();
    }

    /**
     * Get replacement for $VENDOR$.
     */
    protected function getVendorReplacement(): string
    {
        return $this->plugin->config('composer.vendor');
    }

    /**
     * Get replacement for $PLUGIN_NAMESPACE$.
     */
    protected function getPluginNamespaceReplacement(): string
    {
        return str_replace('\\', '\\\\', $this->plugin->config('namespace'));
    }

    protected function getProviderNamespaceReplacement(): string
    {
        return str_replace('\\', '\\\\', GenerateConfigReader::read('provider')->getNamespace());
    }

    public function __get($fskey): mixed
    {
        if ($fskey === 'plugin') {
            // get Plugin Fskey from Namespace: Plugin\DemoTest => DemoTest
            $namespace = str_replace('\\', '/', app()->getNamespace());
            $namespace = rtrim($namespace, '/');
            $pluginFskey = basename($namespace);

            // when running in rootDir
            if ($pluginFskey == 'App') {
                $pluginFskey = null;
            }

            if (empty($this->plugin)) {
                $this->plugin = new \Fresns\PluginManager\Plugin($pluginFskey);
            }

            return $this->plugin;
        }

        throw new \RuntimeException("unknown property $fskey");
    }
}
