<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

return [
    'namespace' => $pluginsNamespace = 'Plugins',

    // YOU COULD CUSTOM HERE
    'namespaces' => [
        $pluginsNamespace => [
            base_path('plugins'),
        ],
    ],

    'autoload_files' => [
        base_path('vendor/fresns/plugin-manager/src/Helpers.php'),
    ],

    'merge_plugin_config' => [
        'include' => [
            ltrim(str_replace(base_path(), '', base_path('plugins/*/composer.json')), '/'),
        ],
        'recurse' => true,
        'replace' => false,
        'ignore-duplicates' => false,
        'merge-dev' => true,
        'merge-extra' => true,
        'merge-extra-deep' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Composer File Template
    |--------------------------------------------------------------------------
    |
    | YOU COULD CUSTOM HERE
    |
    */
    'composer' => [
        'vendor' => 'fresns',
        'author' => [
            [
                'name' => 'Jevan Tang',
                'email' => 'jevan@fresns.org',
                'homepage' => 'https://github.com/jevantang',
                'role' => 'Creator',
            ],
        ],
    ],

    'paths' => [
        'base' => base_path('plugins'),
        'unzip_target_path' => base_path('storage/plugins/.tmp'),
        'backups' => base_path('storage/plugins/backups'),
        'plugins' => base_path('plugins'),
        'assets' => public_path('assets'),
        'migration' => base_path('database/migrations'),

        'generator' => [
            'command' => ['path' => 'app/Console', 'generate' => false],
            'controller' => ['path' => 'app/Http/Controllers', 'generate' => false],
            'filter' => ['path' => 'app/Http/Middleware', 'generate' => false],
            'request' => ['path' => 'app/Http/Requests', 'generate' => false],
            'resource' => ['path' => 'app/Http/Resources', 'generate' => false],
            'model' => ['path' => 'app/Models', 'generate' => true],
            'provider' => ['path' => 'app/Providers', 'generate' => true],
            'policies' => ['path' => 'app/Policies', 'generate' => false],
            'repository' => ['path' => 'app/Repositories', 'generate' => false],
            'event' => ['path' => 'app/Events', 'generate' => false],
            'listener' => ['path' => 'app/Listeners', 'generate' => false],
            'rules' => ['path' => 'app/Rules', 'generate' => false],
            'jobs' => ['path' => 'app/Jobs', 'generate' => false],
            'emails' => ['path' => 'app/Mail', 'generate' => false],
            'notifications' => ['path' => 'app/Notifications', 'generate' => false],
            'config' => ['path' => 'config', 'generate' => true],
            'migration' => ['path' => 'database/migrations', 'generate' => true],
            'seeder' => ['path' => 'database/seeders', 'generate' => true],
            'factory' => ['path' => 'database/factories', 'generate' => true],
            'routes' => ['path' => 'routes', 'generate' => true],
            'assets' => ['path' => 'resources/assets', 'generate' => true],
            'lang' => ['path' => 'resources/lang', 'generate' => true],
            'views' => ['path' => 'resources/views', 'generate' => true],
            'test' => ['path' => 'tests/Unit', 'generate' => true],
            'test-feature' => ['path' => 'tests/Feature', 'generate' => true],
        ],
    ],

    'stubs' => [
        'path' => dirname(__DIR__).'/src/Commands/stubs',
        'files' => [
            'app/Http/Controllers/setting-controller' => 'app/Http/Controllers/$STUDLY_NAME$SettingController.php',
            'app/Providers/provider' => 'app/Providers/$STUDLY_NAME$ServiceProvider.php',
            'app/Providers/command-provider' => 'app/Providers/CommandServiceProvider.php',
            'app/Providers/route-provider' => 'app/Providers/RouteServiceProvider.php',
            'config/config' => 'config/$KEBAB_NAME$.php',
            'database/migrations/init_plugin_config' => 'database/migrations/init_$SNAKE_NAME$_config.php',
            'database/seeders/seeder' => 'database/seeders/DatabaseSeeder.php',
            'resources/assets/css/fresns' => 'resources/assets/css/fresns.css',
            'resources/assets/js/fresns' => 'resources/assets/js/fresns.js',
            'resources/views/layouts/master' => 'resources/views/layouts/master.blade.php',
            'resources/views/layouts/header' => 'resources/views/layouts/header.blade.php',
            'resources/views/layouts/footer' => 'resources/views/layouts/footer.blade.php',
            'resources/views/layouts/tips' => 'resources/views/layouts/tips.blade.php',
            'resources/views/app' => 'resources/views/app.blade.php',
            'resources/views/index' => 'resources/views/index.blade.php',
            'resources/views/setting' => 'resources/views/setting.blade.php',
            'routes/web' => 'routes/web.php',
            'routes/api' => 'routes/api.php',
            'package.json' => 'package.json',
            'composer.json' => 'composer.json',
            'plugin.json' => 'plugin.json',
            'readme' => 'README.md',
            'gitignore' => '.gitignore',
        ],
        'gitkeep' => true,
    ],

    'manager' => [
        'default' => [
            'file' => base_path('fresns.json'),
        ],
    ],
];
