<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Support;

use Fresns\PluginManager\Models\Plugin as PluginModel;

class ThemeAddToDatabase extends PluginAddToDatabase
{
    public function getDataFromPluginJson()
    {
        // get json data
        $data = $this->plugin->json('theme.json')->toArray();
        $data['type'] = PluginConstant::PLUGIN_TYPE_THEME;

        return $data;
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
            'functions' => 'required|boolean',
        ];
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
            'type' => 4,
            'name' => $pluginJsonData['name'],
            'description' => $pluginJsonData['description'] ?? '',
            'version' => $pluginJsonData['version'] ?? '',
            'author' => $pluginJsonData['author'] ?? '',
            'author_link' => $pluginJsonData['authorLink'] ?? null,
            'theme_functions' => $pluginJsonData['functions'] ?? false,
            'is_enable' => PluginModel::PLUGIN_TYPE_DEACTIVATE,
        ]);
    }
}
