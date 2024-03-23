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

    public function fixFilesChineseName($sourcePath)
    {
        $encoding_list = [
            'ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5',
        ];

        try {
            $zip = new \ZipArchive();
            $openResult = $zip->open($sourcePath);
            if ($openResult !== true) {
                throw new \Exception('Cannot Open zip file: '.$sourcePath);
            }
            $fileNum = $zip->numFiles;

            $files = [];
            for ($i = 0; $i < $fileNum; $i++) {
                $statInfo = $zip->statIndex($i, \ZipArchive::FL_ENC_RAW);

                $encode = mb_detect_encoding($statInfo['name'], $encoding_list);
                $string = mb_convert_encoding($statInfo['name'], 'UTF-8', $encode);

                $zip->renameIndex($i, $string);
                $newStatInfo = $zip->statIndex($i, \ZipArchive::FL_ENC_RAW);

                $files[] = $newStatInfo;
            }
        } catch (\Throwable $e) {
            throw $e;
        } finally {
            $zip->close();
        }

        return $files;
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
        $targetPath = $targetPath ?? config('plugins.paths.unzip_target_path');
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

            // Make sure the directory decompression level is the top level of the plugin directory
            $targetPath = $this->ensureDoesntHaveSubdir($targetPath);

            return $targetPath;
        }

        if ($type == 2) {
            $this->fixFilesChineseName($sourcePath);

            // unzip
            $zipFile = $this->zipFile->openFile($sourcePath);
            $zipFile->extractTo($targetPath);

            // Make sure the directory decompression level is the top level of the plugin directory
            $targetPath = $this->ensureDoesntHaveSubdir($targetPath);

            // Decompress to the specified directory
            return $targetPath;
        }

        return null;
    }

    public function ensureDoesntHaveSubdir(string $targetPath): string
    {
        $targetPath = $targetPath ?? config('plugins.paths.unzip_target_path');

        $pattern = sprintf('%s/*', rtrim($targetPath, DIRECTORY_SEPARATOR));

        $files = [];
        foreach (File::glob($pattern) as $file) {
            if (str_contains($file, '__MACOSX')) {
                continue;
            }

            $files[] = $file;
        }

        $fileCount = count($files);
        if (1 < $fileCount && $fileCount <= 3) {
            throw new \RuntimeException("Cannot handle the zip file, zip file count is: {$fileCount}, extract path is: {$targetPath}");
        }

        $tmpDir = $targetPath.'-subdir';
        File::ensureDirectoryExists($tmpDir);

        $firstEntryname = File::basename(current($files));

        $path = $targetPath."/{$firstEntryname}";
        $tmpTargetPath = $tmpDir."/{$firstEntryname}";
        $parentDir = dirname($tmpTargetPath);
        File::ensureDirectoryExists($parentDir);

        if (is_dir($path)) {
            File::copyDirectory($path, $tmpDir);
        } else {
            File::copyDirectory(dirname($path), $parentDir);
        }

        File::cleanDirectory($targetPath);
        File::copyDirectory($tmpDir, $targetPath);
        File::deleteDirectory($tmpDir);

        return $targetPath;
    }
}
