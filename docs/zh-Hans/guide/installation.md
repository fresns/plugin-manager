# 安装

## 要求

| 环境 | 要求 |
| --- | --- |
| Laravel 版本 | 9.x / 10.x / 11.x |
| PHP 版本 | 8.0 或更高版本 |

## 安装

```bash
composer require fresns/plugin-manager
```

## 配置

### 插件管理器配置文件

- 发布指令

```bash
php artisan vendor:publish --provider="Fresns\PluginManager\Providers\PluginServiceProvider"
```

### 主程序 composer.json 配置

> 插件管理器会自动添加

```json
{
    "extra": {
        "merge-plugin": {
            "include": [
                "plugins/*/composer.json"
                // The windows system is: \\plugins\\*\\composer.json
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

### 目录结构

```php
laravel/            // 主程序
├── config/             // 配置文件目录
│   └── plugins.php         // 插件配置文件
├── plugins/            // 插件目录
└── fresns.json         // 插件启用停用状态
```
