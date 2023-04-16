# 命令字开发

插件作为一个独立功能模块，从系统设计和业务封装的角度，采用的是「[命令字](https://github.com/fresns/cmd-word-manager)」作为通讯模式，即一个插件模块包括多个命令字，外部通过命令字方式来调用插件的功能。插件与主程序之间，插件与插件之间，均通过命令字通讯。

## 生成

```php
fresns make:cmd-word-provider
```

进入插件目录，执行生成「命令字提供者」文件。

## 注册

将服务提供者添加到 `plugin.json` 文件中的 `providers` 数组参数中。

```json
{
    //在 plugin.json 文件中找到 providers
    "providers": [
        "Plugins\\DemoPlugin\\Providers\\CmdWordServiceProvider"
    ]
}
```

## 映射

在命令字提供者文件 `/plugins/DemoPlugin/Providers/CmdWordServiceProvider.php` 的属性里 `$cmdWordsMap` 中，添加命令字映射配置。

```php
<?php

namespace Plugins\DemoPlugin\Providers;

use Plugins\DemoPlugin\Models\SendCode;
use Plugins\DemoPlugin\Services\CheckCode;
use Plugins\DemoPlugin\Services\SendEmail;

class CmdWordServiceProvider extends ServiceProvider implements \Fresns\CmdWordManager\Contracts\CmdWordProviderContract
{
    <...>
    protected $unikeyName = 'DemoPlugin';

    protected $cmdWordsMap = [
        ['word' => 'sendCode', 'provider' => [SendCode::class, 'handleSendCode']],
        ['word' => 'checkCode', 'provider' => [CheckCode::class, 'handleCheckCode']],
        ['word' => 'sendEmail', 'provider' => [SendEmail::class, 'handleSendEmail']],
    ];
    <...>
}
```

## 输出

### 处理成功时输出

```php
public function sendCode($wordBody)
{
    <...>

    return [
        'code' => 0, // 错误码，成功为 0
        'message' => 'success',
        'data' => [
            // 处理结果数据
        ]
    ];
}
```

### 处理失败时输出

```php
public function sendCode($wordBody)
{
    <...>

    return [
        'code' => 21005, // 错误码
        'message' => '命令字请求参数错误', // 错误描述
        'data' => [
            // 错误数据
        ]
    ];
}
```

## 使用

每次修改 `plugin.json` 配置信息，需要重启插件，以便应用最新配置。
