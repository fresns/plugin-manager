# 控制指令

## 解压插件包

Unzip the plugin files into the `/plugins/` directory, the final directory will be `/plugins/{fskey}/`.

```php
fresns plugin:unzip /www/wwwroot/fresns/storage/plugins/123e4567-e89b-12d3-a456-426614174000.zip
```

或者

```php
php artisan plugin:unzip /www/wwwroot/fresns/storage/plugins/123e4567-e89b-12d3-a456-426614174000.zip
```

## 发布插件

Publish static resources for the plugin `DemoPlugin`.

```php
fresns plugin:publish
```

或者

```php
php artisan plugin:publish DemoPlugin
```

- `/plugins/DemoPlugin/Resources/assets/` Distribute to web directories `/public/assets/plugins/DemoPlugin/`

## 撤销插件

Unpublish static resources for the plugin `DemoPlugin`.

```php
fresns plugin:unpublish
```

或者

```php
php artisan plugin:unpublish DemoPlugin
```

- `/plugins/DemoPlugin/Resources/assets/` Distribute to web directories `/public/assets/plugins/DemoPlugin/`

## 更新插件 Composer 依赖

Composer all plugins.

```php
fresns plugin:composer-update
```

或者

```php
php artisan plugin:composer-update
```

## 执行插件 Migrate

Migrate the given plugin, or without a plugin an argument, migrate all plugins.

```php
fresns plugin:migrate
```

或者

```php
php artisan plugin:migrate DemoPlugin
```

## 回滚插件 Migrate

Rollback the given plugin, or without an argument, rollback all plugins.

```php
fresns plugin:migrate-rollback
```

或者

```php
php artisan plugin:migrate-rollback DemoPlugin
```

## 刷新插件 Migrate

Refresh the migration for the given plugin, or without a specified plugin refresh all plugins migrations.

```php
fresns plugin:migrate-refresh
```

或者

```php
php artisan plugin:migrate-refresh DemoPlugin
```

## 重置插件 Migrate

Reset the migration for the given plugin, or without a specified plugin reset all plugins migrations.

```php
fresns plugin:migrate-reset
```

或者

```php
php artisan plugin:migrate-reset DemoPlugin
```

## 执行插件 Seed

Seed the given plugin, or without an argument, seed all plugins.

```php
fresns plugin:seed
```

或者

```php
php artisan plugin:seed DemoPlugin
```

## 安装插件

Execute the `plugin:unzip`、`plugin:composer-update`、`plugin:migrate`、`plugin:publish` commands in that order.

```php
fresns plugin:install /www/wwwroot/fresns/storage/plugins/123e4567-e89b-12d3-a456-426614174000.zip
```

或者

```php
php artisan plugin:install /www/wwwroot/fresns/storage/plugins/123e4567-e89b-12d3-a456-426614174000.zip
```

`plugin:publish` 文件分发和入库在最后执行，如果为升级插件，可在入库前，获取数据库旧信息判断插件是否存在以及旧版本号。如果插件有跨版本特殊安装处理，可凭此判断新版和旧版之间的差距。

## 卸载插件

Uninstall the plugin and select whether you want to clean the data of the plugin.

```php
fresns plugin:uninstall --cleandata=true
fresns plugin:uninstall --cleandata=false
```

或者

```php
php artisan plugin:uninstall DemoPlugin --cleandata=true
php artisan plugin:uninstall DemoPlugin --cleandata=false
```

- `/plugins/DemoPlugin/` Physically deletion the folder.
- `/public/assets/plugins/DemoPlugin/` Physically deletion the folder.
- Remove the plugin composer dependency package (skip if the main application or another plugin is in use)
- Logically deletion the value of the record where the `fskey` column of the `plugins` table is `DemoPlugin`.
