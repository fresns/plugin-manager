<?php

namespace Fresns\PluginManager\Providers;

use Carbon\Laravel\ServiceProvider;
use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\Support\Repositories\FileRepository;

class ContractsServiceProvider extends ServiceProvider
{
    /**
     * Register some binding.
     */
    public function register()
    {
        $this->app->bind(RepositoryInterface::class, FileRepository::class);
    }
}
