<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Plugin;
use Fresns\PluginManager\Support\Config\GenerateConfigReader;
use Fresns\PluginManager\Support\Process;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class NewCommand extends Command
{
    use Traits\StubTrait;

    protected $signature = 'new {name}
        {--force}
        ';

    protected $description = 'Create a new laravel package、extension、plugin';

    /**
     * The laravel filesystem instance.
     *
     * @var Filesystem
     */
    protected $filesystem;

    protected $plugin;

    /**
     * @var string
     */
    protected $pluginName;

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function handle()
    {
        $this->filesystem = $this->laravel['files'];
        $this->pluginName = Str::afterLast($this->argument('name'), '/');

        $this->plugin = new Plugin($this->pluginName);

        // clear directory or exit when plugin exists.
        if (File::exists($this->plugin->getPluginPath())) {
            if (! $this->option('force')) {
                $this->error("Plugin {$this->plugin->getUnikey()} exists");

                return Command::FAILURE;
            }

            File::deleteDirectory($this->plugin->getPluginPath());
        }

        $this->generateFolders();
        $this->generateFiles();

        // composer dump-autoload
        Process::run('composer dump-autoload', $this->output);

        $this->info("Package [{$this->pluginName}] created successfully");

        return Command::SUCCESS;
    }

    /**
     * Get the list of folders will created.
     *
     * @return array
     */
    public function getFolders()
    {
        return config('plugins.paths.generator');
    }

    /**
     * Generate the folders.
     */
    public function generateFolders()
    {
        foreach ($this->getFolders() as $key => $folder) {
            $folder = GenerateConfigReader::read($key);

            if ($folder->generate() === false) {
                continue;
            }

            $path = config('plugins.paths.plugins').'/'.$this->argument('name').'/'.$folder->getPath();

            $this->filesystem->makeDirectory($path, 0755, true);
            if (config('plugins.stubs.gitkeep')) {
                $this->generateGitKeep($path);
            }
        }
    }

    /**
     * Generate git keep to the specified path.
     *
     * @param  string  $path
     */
    public function generateGitKeep($path)
    {
        $this->filesystem->put($path.'/.gitkeep', '');
    }

    /**
     * Remove git keep from the specified path.
     *
     * @param  string  $path
     */
    public function removeParentDirGitKeep(string $path)
    {
        if (config('plugins.stubs.gitkeep')) {
            $dirName = dirname($path);
            if (count($this->filesystem->glob("$dirName/*")) >= 1) {
                $this->filesystem->delete("$dirName/.gitkeep");
            }
        }
    }

    /**
     * Get the list of files will created.
     *
     * @return array
     */
    public function getFiles()
    {
        return config('plugins.stubs.files');
    }

    /**
     * Generate the files.
     */
    public function generateFiles()
    {
        foreach ($this->getFiles() as $stub => $file) {
            $pluginName = $this->argument('name');

            $path = config('plugins.paths.plugins').'/'.$pluginName.'/'.$file;

            if ($keys = $this->getReplaceKeys($path)) {
                $file = $this->getReplacedContent($file, $keys);
                $path = $this->getReplacedContent($path, $keys);
            }

            if (! $this->filesystem->isDirectory($dir = dirname($path))) {
                $this->filesystem->makeDirectory($dir, 0775, true);
                $this->removeParentDirGitKeep($dir);
            }

            $this->filesystem->put($path, $this->getStubContents($stub));
            $this->removeParentDirGitKeep($path);

            $this->info("Created : {$path}");
        }
    }
}
