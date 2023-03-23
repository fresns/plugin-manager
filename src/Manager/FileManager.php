<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Manager;

use Fresns\PluginManager\Support\Json;

class FileManager
{
    protected $file;

    protected $status = [];

    protected $pluginsJson;

    public function __construct()
    {
        $this->file = config('plugins.manager.default.file');

        $this->pluginsJson = Json::make($this->file);

        $this->status = $this->pluginsJson->get('plugins');
    }

    public function all()
    {
        return $this->status;
    }

    public function install(string $plugin)
    {
        $this->status[$plugin] = false;

        return $this->write();
    }

    public function uninstall(string $plugin)
    {
        unset($this->status[$plugin]);

        return $this->write();
    }

    public function activate(string $plugin)
    {
        $this->status[$plugin] = true;

        return $this->write();
    }

    public function deactivate(string $plugin)
    {
        $this->status[$plugin] = false;

        return $this->write();
    }

    public function isActivate(string $plugin)
    {
        if (array_key_exists($plugin, $this->status)) {
            return $this->status[$plugin] == true;
        }

        return false;
    }

    public function isDeactivate(string $plugin)
    {
        return ! $this->isActivate($plugin);
    }

    public function write(): bool
    {
        $data = $this->pluginsJson->get();
        $data['plugins'] = $this->status;

        try {
            $content = json_encode(
                $data,
                \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE | \JSON_PRETTY_PRINT | \JSON_FORCE_OBJECT
            );

            return (bool) file_put_contents($this->file, $content);
        } catch (\Throwable $e) {
            info('Failed to update plugin status: %s'.$e->getMessage());

            return false;
        }
    }
}
