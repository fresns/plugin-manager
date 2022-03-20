<?php

namespace $NAMESPACE$;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class $CLASS$ extends ServiceProvider
{
    /**
     * The plugin namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    // protected string $pluginNamespace = '$PLUGIN_NAMESPACE$\$PLUGIN$\$CONTROLLER_NAMESPACE$';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            // ->namespace($this->pluginNamespace)
            ->group(plugin_path('$PLUGIN$', '$WEB_ROUTES_PATH$'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            // ->namespace($this->pluginNamespace)
            ->group(plugin_path('$PLUGIN$', '$API_ROUTES_PATH$'));
    }
}
