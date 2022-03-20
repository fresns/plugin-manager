<?php

namespace Fresns\PluginManager\Listeners\PluginInstall;

use Fresns\PluginManager\Listeners\PluginEventFilter;
use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Models\Plugin as PluginModel;
use Fresns\PluginManager\Support\PluginConstant;

class ThemeAddToDatabase extends PluginEventFilter
{
    protected $type = PluginConstant::PLUGIN_TYPE_THEME;

    public function handleEvent(Plugin $plugin)
    {
        /** @var Plugin */
        $this->plugin = $plugin;


        // validate theme.json is valid
        $data = $this->ensureThemeJsonIsValid();

        // save plugin.json to database: plugins
        $this->saveThemeToDatabase($data);
    }

    public function refreshPlugin()
    {
        // refresh plugin model
        $this->plugin = app('plugins.repository')
            ->addLocation(config('plugins.paths.themes'))
            ->findOrFail($this->plugin->getName());

        return $this;
    }

    public function getDataFromPluginJson()
    {
        // get theme.json data
        return $this->plugin->json('theme.json')->toArray();
    }

    public function ensureThemeJsonIsValid()
    {
        $this->refreshPlugin();

        $data = $this->getDataFromPluginJson();

        try {
            validator()->validate($data, [
                'unikey' => 'required|string',
                'name' => 'required|string',
                'description' => 'nullable|string',
                'version' => 'required|string',
                'author' => 'nullable|string',
                'authorLink' => 'nullable|string',
                'functions' => 'required|boolean',
            ]);
        } catch (\Throwable $e) {
            throw new \RuntimeException($e->validator->errors()->first());
        }

        return $data;
    }

    /**
     * This function will save the theme data to the database
     * 
     * @param array pluginJsonData The data that was extracted from the plugin's JSON file.
     * 
     * @return The plugin model object.
     */
    public function saveThemeToDatabase(array $pluginJsonData)
    {
        $plugin = PluginModel::withTrashed()->where('unikey', $pluginJsonData['unikey'])->first();

        if ($plugin?->trashed()) {
            return $plugin->restore();
        }

        return PluginModel::create([
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
