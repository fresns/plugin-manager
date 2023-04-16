# Installation and Setup

## Requirements

| Environment | Requirements |
| --- | --- |
| Laravel | 9.x or 10.x |
| PHP | 8.0 or higher |

## Installation

```bash
composer require fresns/plugin-manager
```

## Configuration

1. Publish plugin manager config file

```bash
php artisan vendor:publish --provider="Fresns\PluginManager\Providers\PluginServiceProvider"
```

```php
laravel/            // Main Program
├── config/             // Configuration file directory
│   └── plugins.php         // Plugin config file
├── extensions/         // Extensions directory
│   ├── plugins/            // Plugin directory
│   └── backups/            // Backup directory
└── fresns.json         // Plugin activate and deactivate status
```

2. Main program `composer.json` Add configuration

```json
{
    "extra": {
        "merge-plugin": {
            "include": [
                "extensions/plugins/*/composer.json"
            ],
            "recurse": true,
            "replace": false,
            "ignore-duplicates": false,
            "merge-dev": true,
            "merge-extra": true,
            "merge-extra-deep": true
        }
    },
    "config": {
        "allow-plugins": {
            "wikimedia/composer-merge-plugin": true
        }
    }
}
```
