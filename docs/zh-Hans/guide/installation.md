# 安装

## 要求

| 环境 | 要求 |
| --- | --- |
| Laravel 版本 | 9.x 或 10.x |
| PHP 版本 | 8.0 或更高版本 |

## 安装

```bash
composer require fresns/plugin-manager
```

## 配置

1、发布插件管理器配置文件

```bash
php artisan vendor:publish --provider="Fresns\PluginManager\Providers\PluginServiceProvider"
```

```php
laravel/            // 主程序
├── config/             // 配置文件目录
│   └── plugins.php         // 插件配置文件
├── extensions/         // 扩展目录
│   ├── plugins/            // 插件目录
│   └── backups/            // 备份目录
└── fresns.json         // 插件启用停用状态
```

2、主程序 composer.json 添加配置

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
