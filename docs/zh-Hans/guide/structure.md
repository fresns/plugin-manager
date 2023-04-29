# 插件结构

## 目录结构

```php
laravel/            // 主程序
└── extensions/         // 扩展目录（插件和主题）
    └── plugins/            // 插件目录
        └── DemoPlugin/         // 示例插件
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
    "unikey": "DemoPlugin", // 唯一标识，大驼峰
    "name": "演示插件", // 名字
    "description": "这是演示插件", // 描述
    "developer": "唐杰", // 开发者
    "website": "https://tangjie.me", // 开发者主页
    "version": "1.0.0", // 语义化版本号
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

# 插件监听事件

```php
protected $listen = [
    // 安装中
    'plugin:installing' => [
        //
    ],

    // 插件安装完成
    'plugin:installed' => [
        // 
    ],

    // 插件启动中
    'plugin:activating' => [
        //
    ],

    // 插件启用完成
    'plugin:activated' => [
        //
    ],

    // 插件停用中
    'plugin:deactivating' => [
        //
    ],

    // 插件停用完成
    'plugin:deactivated' => [
        //
    ],

    // 插件卸载中
    'plugin:uninstalling' => [
        //
    ],

    // 插件卸载完成
    'plugin:uninstalled' => [
        //
    ],
];
```

## 资源文件分发

插件安装发布时，将静态资源分发到 public 目录。

| 插件文件夹 | 分发到站点资源目录 |
| --- | --- |
| /extensions/plugins/`{unikey}`/Resources/assets/ | /public/assets/plugins/`{unikey}`/ |
