<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Support;

use Illuminate\Support\Facades\File;
use PhpZip\ZipFile;

class Zip
{
    protected $zipFile;

    public function __construct()
    {
        $this->zipFile = new ZipFile();
    }

    public function pack(string $sourcePath, ?string $filename = null, ?string $targetPath = null): ?string
    {
        if (! File::exists($sourcePath)) {
            throw new \RuntimeException("Directory to be decompressed does not exist {$sourcePath}");
        }

        $filename = $filename ?? File::name($sourcePath);
        $targetPath = $targetPath ?? File::dirname($sourcePath);
        $targetPath = $targetPath ?: File::dirname($sourcePath);

        File::ensureDirectoryExists($targetPath);

        $zipFilename = str_contains($filename, '.zip') ? $filename : $filename.'.zip';
        $zipFilepath = "{$targetPath}/{$zipFilename}";

        while (File::exists($zipFilepath)) {
            $basename = File::name($zipFilepath);
            $zipCount = count(File::glob("{$targetPath}/{$basename}*.zip"));

            $zipFilename = $basename.$zipCount.'.zip';
            $zipFilepath = "{$targetPath}/{$zipFilename}";
        }

        // Compression
        $this->zipFile->addDirRecursive($sourcePath, $filename);
        $this->zipFile->saveAsFile($zipFilepath);

        return $targetPath;
    }

    public function unpack(string $sourcePath, ?string $targetPath = null): ?string
    {
        try {
            // Detects the file type and unpacks only zip files
            $mimeType = File::mimeType($sourcePath);
        } catch (\Throwable $e) {
            \info("Unzip failed {$e->getMessage()}");
            throw new \RuntimeException("Unzip failed {$e->getMessage()}");
        }

        // Get file types (only directories and zip files are processed)
        $type = match (true) {
            default => null,
            str_contains($mimeType, 'directory') => 1,
            str_contains($mimeType, 'zip') => 2,
        };

        if (is_null($type)) {
            \info("unsupport mime type $mimeType");
            throw new \RuntimeException("unsupport mime type $mimeType");
        }

        // Make sure the unzip destination directory exists
        $targetPath = $targetPath ?? storage_path('app/extensions/.tmp');
        if (empty($targetPath)) {
            \info('targetPath cannot be empty');
            throw new \RuntimeException('targetPath cannot be empty');
        }

        if (! is_dir($targetPath)) {
            File::ensureDirectoryExists($targetPath);
        }

        if ($targetPath == $sourcePath) {
            return $targetPath;
        }

        // Empty the directory to avoid leaving files of other plugins
        File::cleanDirectory($targetPath);

        // Directory without unzip operation, copy the original directory to the temporary directory
        if ($type == 1) {
            File::copyDirectory($sourcePath, $targetPath);

            // Make sure the directory decompression level is the top level of the theme directory
            $targetPath = $this->ensureDoesntHaveSubdir($targetPath);

            return $targetPath;
        }

        if ($type == 2) {
            // unzip
            $zipFile = $this->zipFile->openFile($sourcePath);
            $zipFile->extractTo($targetPath);

            // Make sure the directory decompression level is the top level of the theme directory
            $targetPath = $this->ensureDoesntHaveSubdir($targetPath);

            // Decompress to the specified directory
            return $targetPath;
        }

        return null;
    }

    public function ensureDoesntHaveSubdir(string $targetPath): string
    {
        $targetPath = $targetPath ?? storage_path('app/extensions/.tmp');

        $pattern = sprintf('%s/*', rtrim($targetPath, DIRECTORY_SEPARATOR));

        $files = [];
        foreach (File::glob($pattern) as $file) {
            if (str_contains($file, '__MACOSX')) {
                continue;
            }

            $files[] = $file;
        }

        if (count($files) !== 1) {
            throw new \RuntimeException("Unable to find the directory where the plugin is located: $targetPath");
        }

        $tmpDir = $targetPath.'-subdir';
        File::ensureDirectoryExists($tmpDir);

        $firstEntryname = File::name(current($files));

        File::copyDirectory($targetPath."/{$firstEntryname}", $tmpDir);
        File::cleanDirectory($targetPath);
        File::copyDirectory($tmpDir, $targetPath);
        File::deleteDirectory($tmpDir);

        return $targetPath;
    }
}
