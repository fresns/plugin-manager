<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

return [

    'namespace' => 'Plugins',

    'stubs' => [
        // Customize the path location of the stub release file, which is determined by stubs.path when it is turned on.
        // See also: Fresns\PluginManager\Providers\PluginServiceProvider:66
        'enabled' => false,
        'path' => base_path('vendor/fresns/plugin-manager/stubs'),
        'files' => [
            'routes/web' => 'Routes/web.php',
            'routes/api' => 'Routes/api.php',
            'views/index' => 'Resources/views/index.blade.php',
            'views/master' => 'Resources/views/layouts/master.blade.php',
            'scaffold/config' => 'Config/config.php',
            'assets/js/app' => 'Resources/assets/js/app.js',
            'assets/sass/app' => 'Resources/assets/sass/app.scss',
            'webpack' => 'webpack.mix.js',
            'package' => 'package.json',
            'gitignore' => '.gitignore',
        ],
        'replacements' => [
            'routes/web' => ['KEBAB_NAME', 'STUDLY_NAME', 'PLUGIN_NAMESPACE', 'PLUGIN', 'CONTROLLER_NAMESPACE'],
            'routes/api' => ['KEBAB_NAME', 'STUDLY_NAME', 'PLUGIN_NAMESPACE', 'PLUGIN', 'CONTROLLER_NAMESPACE'],
            'json' => ['KEBAB_NAME', 'STUDLY_NAME', 'PLUGIN_NAMESPACE', 'PROVIDER_NAMESPACE'],
            'views/index' => ['KEBAB_NAME'],
            'views/master' => ['KEBAB_NAME', 'STUDLY_NAME'],
            'scaffold/config' => ['STUDLY_NAME'],
            'webpack' => ['KEBAB_NAME'],
        ],
        'gitkeep' => true,
    ],
    'paths' => [

        // Plugin running directory
        'plugins' => base_path('plugins'),

        // theme running directory
        'themes' => resource_path('themes'),

        // Resource distribution directory
        'assets' => public_path('assets'),

        // Default plugin creation directory structure
        'generator' => [
            'config' => ['path' => 'Config', 'generate' => true],
            'command' => ['path' => 'Console', 'generate' => false],
            'factory' => ['path' => 'Database/Factories', 'generate' => false],
            'migration' => ['path' => 'Database/Migrations', 'generate' => true],
            'seeder' => ['path' => 'Database/Seeders', 'generate' => true],
            'dto' => ['path' => 'DTO', 'generate' => false],
            'event' => ['path' => 'Events', 'generate' => false],
            'controller' => ['path' => 'Http/Controllers', 'generate' => true],
            'middleware' => ['path' => 'Http/Middleware', 'generate' => true],
            'request' => ['path' => 'Http/Requests', 'generate' => true],
            'resource' => ['path' => 'Http/Resources', 'generate' => false],
            'provider' => ['path' => 'Providers', 'generate' => true],
            'assets' => ['path' => 'Resources/assets', 'generate' => true],
            'lang' => ['path' => 'Resources/lang', 'generate' => true],
            'views' => ['path' => 'Resources/views', 'generate' => true],
            'routes' => ['path' => 'Routes', 'generate' => true],
            'notifications' => ['path' => 'Notifications', 'generate' => false],
            'emails' => ['path' => 'Emails', 'generate' => false],
            'rules' => ['path' => 'Rules', 'generate' => false],
            'listener' => ['path' => 'Listeners', 'generate' => false],
            'jobs' => ['path' => 'Jobs', 'generate' => false],
            'policies' => ['path' => 'Policies', 'generate' => false],
            'model' => ['path' => 'Models', 'generate' => false],
            'test' => ['path' => 'Tests/Unit', 'generate' => true],
            'test-feature' => ['path' => 'Tests/Feature', 'generate' => true],
        ],
    ],
    // Event Listening
    'events' => [
        'plugins.installing' => [
            \Fresns\PluginManager\Listeners\PluginInstall\PluginUnzip::class, // plugin:unzip
            \Fresns\PluginManager\Listeners\PluginInstall\PluginComposerInstall::class, // plugin:composer-install
        ],
        // After installation of the plugin
        'plugins.installed' => [
            \Fresns\PluginManager\Listeners\PluginInstall\PluginMigrate::class, // plugin:migrate
            \Fresns\PluginManager\Listeners\PluginInstall\PluginPublish::class, // plugin:publish or theme:publish, and add plugin.json or theme.json to database
        ],
        // Before the plugin is activated
        'plugins.activating' => [
            \Fresns\PluginManager\Listeners\PluginActivate\PluginActivateFromDatabase::class, // plugin:deactivate
        ],

        // After the plugin is activated
        'plugins.activated' => [

        ],

        // Before the plugin is deactivated
        'plugins.deactivating' => [
            \Fresns\PluginManager\Listeners\PluginDeactivate\PluginDeactivateFromDatabase::class, // plugin:deactivate
        ],

        // After the plugin is deactivated
        'plugins.deactivated' => [

        ],

        // To clear the data, the main program calls after initializing the plugin
        // $plugin->fireEventClearData();
        'plugins.cleardata' => [
            \Fresns\PluginManager\Listeners\PluginUninstall\PluginMigrateRollback::class, // plugin:migrate-rollback
        ],

        // Before uninstalling the plugin
        'plugins.uninstalling' => [
            \Fresns\PluginManager\Listeners\PluginUninstall\PluginComposerRemove::class, // plugin:composer-remove
            \Fresns\PluginManager\Listeners\PluginUninstall\PluginUnpublish::class, // plugin:unpublish, theme:unpublish
            \Fresns\PluginManager\Listeners\PluginUninstall\PluginRemoveFromDatabase::class,
        ],

        // After uninstalling the plugin
        'plugins.uninstalled' => [
        ],
    ],

    // Customized commands
    'commands' => [

    ],

    'cache' => [
        'enabled' => false,
        'key' => 'plugin-manager',
        'lifetime' => 60,
    ],
    'register' => [
        'translations' => true,
        'files' => 'register',
    ],

    'activators' => [
        'file' => [
            'class' => \Fresns\PluginManager\Activators\FileActivator::class,
            'statuses-file' => base_path('fresns.json'),
            'cache-key' => 'activator.installed',
            // 604800 seconds，7 day, 1 week.
            'cache-lifetime' => 604800,
        ],
    ],

    'activator' => 'file',
];
