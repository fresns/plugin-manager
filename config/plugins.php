<?php

/**
 * DON'T CHANGE ANYTHING EXCEPT FOLLOWING
 *
 * 1. namespace
 * 2. composer.*
 *
 */
return [
    'namespace' => $pluginsNamespace = 'Plugins',

    // YOU COULD CUSTOM HERE
    'namespaces' => [
        $pluginsNamespace => [
            base_path('extensions/plugins'),
        ],
    ],

    'autoload_files' => [
        // base_path('demo.php'),
    ],

    'merge_plugin_config' => [
        'include' => [
            base_path('extensions/plugins/*/composer.json'),
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
    'composer'  => [
        'vendor' => 'fresns',
        'author' => [
            [
                'name'  => 'Jarvis Tang',
                'email' => 'jarvis.okay@gmail.com',
                'homepage' => 'https://github.com/jarvis-tang',
                "role" => "Creator",
            ],
            [
                'name'  => 'mouyong',
                'email' => 'my24251325@gmail.com',
                'homepage' => 'https://github.com/mouyong',
                "role" => "Developer",
            ],
        ],
    ],

    'paths' => [
        'base' => base_path('extensions'),
        'backups' => base_path('extensions/backups/plugins'),
        'plugins' => base_path('extensions/plugins'),
        'assets' => public_path('assets/plugins'),
        'migration' => base_path('database/migrations'),

        'generator' => [
            'config'            => ['path' => 'config', 'generate' => true],
            'command'           => ['path' => 'app/Console', 'generate' => false],
            'migration'         => ['path' => 'database/migrations', 'generate' => true],
            'seeder'            => ['path' => 'database/seeders', 'generate' => true],
            'factory'           => ['path' => 'database/factories', 'generate' => true],
            'model'             => ['path' => 'app/Models', 'generate' => true],
            'routes'            => ['path' => 'routes', 'generate' => true],
            'controller'        => ['path' => 'app/Http/Controllers', 'generate' => false],
            'filter'            => ['path' => 'app/Http/Middleware', 'generate' => false],
            'request'           => ['path' => 'app/Http/Requests', 'generate' => false],
            'provider'          => ['path' => 'app/Providers', 'generate' => true],
            'assets'            => ['path' => 'resources/assets', 'generate' => true],
            'lang'              => ['path' => 'resources/lang', 'generate' => true],
            'views'             => ['path' => 'resources/views', 'generate' => true],
            'test'              => ['path' => 'tests/Unit', 'generate' => true],
            'test-feature'      => ['path' => 'tests/Feature', 'generate' => true],
            'repository'        => ['path' => 'app/Repositories', 'generate' => false],
            'event'             => ['path' => 'app/Events', 'generate' => false],
            'listener'          => ['path' => 'app/Listeners', 'generate' => false],
            'policies'          => ['path' => 'app/Policies', 'generate' => false],
            'rules'             => ['path' => 'app/Rules', 'generate' => false],
            'jobs'              => ['path' => 'app/Jobs', 'generate' => false],
            'emails'            => ['path' => 'app/Mail', 'generate' => false],
            'notifications'     => ['path' => 'app/Notifications', 'generate' => false],
            'resource'          => ['path' => 'app/Http/Resources', 'generate' => false],
        ],
    ],

    'stubs' => [
        'path'         => dirname(__DIR__) . '/src/Commands/stubs',
        'files'        => [
            'routes/web'            => 'routes/web.php',
            'routes/api'            => 'routes/api.php',
            'views/index'           => 'resources/views/index.blade.php',
            'views/master'          => 'resources/views/layouts/master.blade.php',
            'scaffold/config'       => 'config/config.php',
            'scaffold/provider'     => 'app/Providers/$STUDLY_NAME$ServiceProvider.php',
            'seeder'                => 'database/seeders/DatabaseSeeder.php',
            'route-provider'        => 'app/Providers/RouteServiceProvider.php',
            'command-provider'      => 'app/Providers/CommandServiceProvider.php',
            'composer.json'         => 'composer.json',
            'plugin.json'           => 'plugin.json',
            'assets/js/app'         => 'resources/assets/js/app.js',
            'assets/sass/app'       => 'resources/assets/sass/app.scss',
            'webpack'               => 'webpack.mix.js',
            'package.json'          => 'package.json',
            'installer'             => 'app/Support/Installer.php',
            'init_plugin_config'    => 'database/migrations/init_$SNAKE_NAME$_config.php',
            'readme'                => 'README.md'
        ],
        'gitkeep'      => true,
    ],

    'manager' => [
        'default' => [
            'file' => base_path('fresns.json'),
        ],
    ],
];
