# Control

## Unzip The Plugin Package

Unzip the plugin files into the `/plugins/` directory, the final directory will be `/plugins/{fskey}/`.

```php
fresns plugin:unzip /www/wwwroot/fresns/storage/plugins/downloads/123e4567-e89b-12d3-a456-426614174000.zip
```

or

```php
php artisan plugin:unzip /www/wwwroot/fresns/storage/plugins/downloads/123e4567-e89b-12d3-a456-426614174000.zip
```

## Publish Plugin

Publish static resources for the plugin `DemoPlugin`.

```php
fresns plugin:publish
```

or

```php
php artisan plugin:publish DemoPlugin
```

- `/plugins/DemoPlugin/Resources/assets/` Distribute to web directories `/public/assets/DemoPlugin/`

## Unpublish

Unpublish static resources for the plugin `DemoPlugin`.

```php
fresns plugin:unpublish
```

or

```php
php artisan plugin:unpublish DemoPlugin
```

- `/plugins/DemoPlugin/Resources/assets/` Distribute to web directories `/public/assets/DemoPlugin/`

## Update Plugin Composer Package

Composer all plugins.

```php
fresns plugin:composer-update
```

or

```php
php artisan plugin:composer-update
```

## Run Plugin Migrate

Migrate the given plugin, or without a plugin an argument, migrate all plugins.

```php
fresns plugin:migrate
```

or

```php
php artisan plugin:migrate DemoPlugin
```

## Rollback Plugin Migrate

Rollback the given plugin, or without an argument, rollback all plugins.

```php
fresns plugin:migrate-rollback
```

or

```php
php artisan plugin:migrate-rollback DemoPlugin
```

## Refresh Plugin Migrate

Refresh the migration for the given plugin, or without a specified plugin refresh all plugins migrations.

```php
fresns plugin:migrate-refresh
```

or

```php
php artisan plugin:migrate-refresh DemoPlugin
```

## Reset Plugin Migrate

Reset the migration for the given plugin, or without a specified plugin reset all plugins migrations.

```php
fresns plugin:migrate-reset
```

or

```php
php artisan plugin:migrate-reset DemoPlugin
```

## Run Plugin Seed

Seed the given plugin, or without an argument, seed all plugins.

```php
fresns plugin:seed
```

or

```php
php artisan plugin:seed DemoPlugin
```

## Install Plugin

Execute the `plugin:unzip`、`plugin:composer-update`、`plugin:migrate`、`plugin:publish` commands in that order.

```php
fresns plugin:install /www/wwwroot/fresns/storage/plugins/123e4567-e89b-12d3-a456-426614174000.zip
```

or

```php
php artisan plugin:install /www/wwwroot/fresns/storage/plugins/123e4567-e89b-12d3-a456-426614174000.zip
```

## Uninstall Plugin

Uninstall the plugin and select whether you want to clean the data of the plugin.

```php
fresns plugin:uninstall --cleardata=true
fresns plugin:uninstall --cleardata=false
```

or

```php
php artisan plugin:uninstall DemoPlugin --cleardata=true
php artisan plugin:uninstall DemoPlugin --cleardata=false
```

- `/plugins/DemoPlugin/` Physically deletion the folder.
- `/public/assets/DemoPlugin/` Physically deletion the folder.
- Remove the plugin composer dependency package (skip if the main application or another plugin is in use)
- Logically deletion the value of the record where the `fskey` column of the `plugins` table is `DemoPlugin`.
