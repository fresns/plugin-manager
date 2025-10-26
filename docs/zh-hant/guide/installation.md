# 安裝

## 要求

| 環境 | 要求 |
| --- | --- |
| Laravel 版本 | 9.x / 10.x / 11.x / 12.x |
| PHP 版本 | 8.0 或更高版本 |

## 安裝

```sh
composer require fresns/plugin-manager
```

## 配置

### 外掛管理器配置檔案

- 發布指令

```sh
php artisan vendor:publish --provider="Fresns\PluginManager\Providers\PluginServiceProvider"
```

### 主程式 composer.json 配置

> 外掛管理器會自動添加

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

### 目錄結構

```php
laravel/            // 主機程式
├── config/             // 設定檔目錄
│   └── plugins.php         // 外掛設定檔
├── plugins/            // 外掛目錄
└── fresns.json         // 外掛啟用停用狀態
```
