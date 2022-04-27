<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Support;

use Fresns\PluginManager\Models\Plugin as PluginModel;

class PluginAddToDatabase
{
    public function handle(Plugin $plugin)
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
        // get json data
        return $this->plugin->json('plugin.json')->toArray();
    }

    public function getPluginDataValidateRule()
    {
        return [
            'unikey' => 'required|string',
            'name' => 'required|string',
            'type' => 'required|integer',
            'description' => 'nullable|string',
            'version' => 'required|string',
            'author' => 'nullable|string',
            'authorLink' => 'nullable|string',
            'scene' => 'nullable|array',
            'accessPath' => 'nullable|string',
            'settingsPath' => 'nullable|string',
        ];
    }

    public function ensurePluginJsonIsValid()
    {
        $this->refreshPlugin();

        $data = $this->getDataFromPluginJson();

        try {
            validator()->validate($data, $this->getPluginDataValidateRule());
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

        return PluginModel::updateOrCreate([
            'unikey' => $pluginJsonData['unikey'],
        ], [
            'unikey' => $pluginJsonData['unikey'],
            'type' => $pluginJsonData['type'],
            'name' => $pluginJsonData['name'],
            'description' => $pluginJsonData['description'] ?? '',
            'version' => $pluginJsonData['version'] ?? '',
            'author' => $pluginJsonData['author'] ?? '',
            'author_link' => $pluginJsonData['authorLink'] ?? null,
            'scene' => $pluginJsonData['scene'] ?? null,
            'access_path' => $pluginJsonData['accessPath'] ?? null,
            'settings_path' => $pluginJsonData['settingsPath'] ?? null,
            'is_upgrade' => 0,
            'is_enable' => PluginModel::PLUGIN_TYPE_DEACTIVATE,
        ]);
    }
}
