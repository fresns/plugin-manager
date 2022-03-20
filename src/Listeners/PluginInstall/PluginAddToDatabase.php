<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Listeners\PluginInstall;

use Fresns\PluginManager\Listeners\PluginEventFilter;
use Fresns\PluginManager\Models\Plugin as PluginModel;
use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Support\PluginConstant;

class PluginAddToDatabase extends PluginEventFilter
{
    protected $type = PluginConstant::PLUGIN_TYPE_EXTENSION;

    public function handleEvent(Plugin $plugin)
    {
        $this->plugin = $plugin;

        // validate plugin.json is valid
        $data = $this->ensurePluginJsonIsValid();

        // save plugin.json to database: plugins
        $this->savePluginToDatabase($data);
    }

    public function refreshPlugin()
    {
        // refresh plugin model
        $this->plugin = app('plugins.repository')->findOrFail($this->plugin->getName());

        return $this;
    }

    public function getDataFromPluginJson()
    {
        // get plugin.json data
        return $this->plugin->json('plugin.json')->toArray();
    }

    public function ensurePluginJsonIsValid()
    {
        $this->refreshPlugin();

        $data = $this->getDataFromPluginJson();

        try {
            validator()->validate($data, [
                'unikey' => 'required|string',
                'name' => 'required|string',
                'type' => 'required|integer',
                'description' => 'nullable|string',
                'version' => 'required|string',
                'author' => 'nullable|string',
                'authorLink' => 'nullable|string',
                'scene' => 'nullable|array',
                'accessPath' => 'nullable|string',
                'settingPath' => 'nullable|string',
            ]);
        } catch (\Throwable $e) {
            throw new \RuntimeException($e->validator->errors()->first());
        }

        return $data;
    }

    /**
     * This function will save the plugin data to the database.
     *
     * @param array pluginJsonData The data that is passed to the savePluginToDatabase function.
     * @return The plugin model object.
     */
    public function savePluginToDatabase(array $pluginJsonData)
    {
        $plugin = PluginModel::withTrashed()->where('unikey', $pluginJsonData['unikey'])->first();

        if ($plugin?->trashed()) {
            return $plugin->restore();
        }

        return PluginModel::create([
            'unikey' => $pluginJsonData['unikey'],
            'type' => $pluginJsonData['type'],
            'name' => $pluginJsonData['name'],
            'description' => $pluginJsonData['description'] ?? '',
            'version' => $pluginJsonData['version'] ?? '',
            'author' => $pluginJsonData['author'] ?? '',
            'author_link' => $pluginJsonData['authorLink'] ?? null,
            'scene' => $pluginJsonData['scene'] ?? null,
            'access_path' => $pluginJsonData['accessPath'] ?? null,
            'setting_path' => $pluginJsonData['settingPath'] ?? null,
            'is_enable' => PluginModel::PLUGIN_TYPE_DEACTIVATE,
        ]);
    }
}
