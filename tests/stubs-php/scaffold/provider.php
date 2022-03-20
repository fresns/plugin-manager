<?php

namespace $NAMESPACE$;

use Illuminate\Support\ServiceProvider;

class $CLASS$ extends ServiceProvider
{
    /**
     * @var string $pluginName
     */
    protected string $pluginName = '$PLUGIN$';

    /**
     * @var string $pluginNameKebab
     */
    protected string $pluginNameKebab = '$KEBAB_NAME$';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            plugin_path($this->pluginName, '$PATH_CONFIG$/config.php') => config_path($this->pluginNameKebab . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            plugin_path($this->pluginName, '$PATH_CONFIG$/config.php'), $this->pluginNameKebab
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/plugins/' . $this->pluginNameKebab);

        $sourcePath = plugin_path($this->pluginName, '$PATH_VIEWS$');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->pluginNameKebab . '-plugin-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->pluginNameKebab);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/plugins/' . $this->pluginNameKebab);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->pluginNameKebab);
        } else {
            $this->loadTranslationsFrom(plugin_path($this->pluginName, '$PATH_LANG$'), $this->pluginNameKebab);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/plugins/' . $this->pluginNameKebab)) {
                $paths[] = $path . '/plugins/' . $this->pluginNameKebab;
            }
        }
        return $paths;
    }
}
