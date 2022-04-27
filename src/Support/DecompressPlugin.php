<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Support;

use Fresns\PluginManager\Console\Commands\PluginUnzipCommand;
use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\Exceptions\DecompressPluginException;
use Illuminate\Filesystem\Filesystem;

class DecompressPlugin
{
    /**
     * @var string
     */
    protected string $compressPath;

    protected RepositoryInterface $repository;

    protected string $tmpDecompressPath;

    protected Filesystem $filesystem;

    protected $jsonFilePath = null;

    public function __construct(string $compressPath, int $type = PluginConstant::PLUGIN_TYPE_EXTENSION)
    {
        $this->compressPath = $compressPath;
        $this->tmpDecompressPath = dirname($this->compressPath).'/.tmp';
        $this->type = $type;

        $this->repository = app('plugins.repository');
        $this->filesystem = app('files');
    }

    public function handle(): ?string
    {
        $archive = new \ZipArchive();

        $op = $archive->open($this->compressPath);

        if ($op !== true) {
            return null;
        }

        $archive->extractTo($this->tmpDecompressPath);

        $archive->close();

        $decompressPath = $this->getDecompressPath();

        $this->filesystem->moveDirectory(
            pathinfo($this->jsonFilePath, PATHINFO_DIRNAME),
            $decompressPath,
            true
        );

        return basename($decompressPath);
    }

    public function getDecompressPath(): string
    {
        $jsonFileName = match ($this->type) {
            PluginConstant::PLUGIN_TYPE_THEME => 'theme.json',
            default => 'plugin.json',
        };

        $files = $this->filesystem->glob("{$this->tmpDecompressPath}/{$jsonFileName}");
        if (count($files) === 0) {
            // Deeper level scanning
            $files = $this->filesystem->glob("{$this->tmpDecompressPath}/**/{$jsonFileName}");

            if (count($files) === 0) {
                throw new DecompressPluginException("{$this->tmpDecompressPath}/{$jsonFileName} parsing error.");
            }
        }

        $this->jsonFilePath = head($files);

        $pluginName = Json::make($this->jsonFilePath)->get('unikey');

        // Back up existing plugins
        PluginUnzipCommand::backupExistsPlugin($pluginName);

        $decompressPath = match ($this->type) {
            PluginConstant::PLUGIN_TYPE_THEME => resource_path("themes/$pluginName"),
            default => base_path("plugins/$pluginName"),
        };

        if (! $this->filesystem->isDirectory($decompressPath)) {
            $this->filesystem->makeDirectory($decompressPath, 0775, true);
        }

        return $decompressPath;
    }

    public function __destruct()
    {
        if ($this->filesystem->isDirectory($this->tmpDecompressPath)) {
            $this->filesystem->delete($this->tmpDecompressPath);
        }
    }
}
