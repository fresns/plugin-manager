# Plugin Structure

## Directory Structure

```php
laravel/            // Main Program
└── extensions/         // Extensions directory (plugins and themes)
    └── plugins/            // Plugin directory
        └── DemoPlugin/         // Demo plugin
            ├── app/
            ├── config/
            ├── database/
            ├── resources/
            │   ├── assets/
            │   │   ├── images/
            │   │   ├── js/
            │   │   └── css/
            │   ├── lang/
            │   └── views/
            ├── routes/
            ├── tests/
            ├── plugin.json
            └── composer.json
```

## plugin.json

```json
{
    "fskey": "DemoPlugin",
    "name": "Demo Plugin",
    "description": "Plugin description",
    "developer": "Jevan Tang",
    "website": "https://github.com/jevantang",
    "version": "1.0.0",
    "providers": [
        "Plugins\\DemoPlugin\\Providers\\DemoPluginServiceProvider",
        "Plugins\\DemoPlugin\\Providers\\CmdWordServiceProvider",
        "Plugins\\DemoPlugin\\Providers\\EventServiceProvider"
    ],
    "autoloadFiles": [
        // autoload files
        "app/Http/Function.php"
    ],
    "aliases": {}
}
```

## composer.json

```json
{
    "name": "fresns/demo-plugin",
    "license": "Apache-2.0",
    "require": {
        "laravel/email": "^2.0"
    },
    "autoload": {
        "psr-4": {
            "Plugins\\DemoPlugin\\": "./"
        }
    }
}
```

# Plugin Listeners

```php
protected $listen = [
    // plugin installing
    'plugin:installing' => [
        //
    ],

    // plugin installed
    'plugin:installed' => [
        // 
    ],

    // plugin activating
    'plugin:activating' => [
        //
    ],

    // plugin activated
    'plugin:activated' => [
        //
    ],

    // plugin deactivating
    'plugin:deactivating' => [
        //
    ],

    // plugin deactivated
    'plugin:deactivated' => [
        //
    ],

    // plugin uninstalling
    'plugin:uninstalling' => [
        //
    ],

    // plugin uninstalled
    'plugin:uninstalled' => [
        //
    ],
];
```

## Assets file publish

Assets are distributed to the public directory when the plugin is installed and released.

| Plugin Folder | Publish to the site resource directory |
| --- | --- |
| /extensions/plugins/`{fskey}`/Resources/assets/ | /public/assets/plugins/`{fskey}`/ |
