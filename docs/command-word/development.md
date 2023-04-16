# Command Word Development

As an independent functional module, the plug-in adopts "[command word](https://github.com/fresns/cmd-word-manager)" as the communication mode from the perspective of system design and business encapsulation, that is, a plug-in module includes multiple command words, and the functions of the plug-in are externally called through the command word mode. Plug-ins communicate with the main program and plug-ins through command words.

## Make

```php
fresns make:cmd-word-provider
```

Go to the plug-in directory and execute to generate the command word provider file.

## Register

Add the service provider to the `providers` array parameter in the `plugin.json` file.

```json
{
    // In the plugin.json file, find the providers
    "providers": [
        "Plugins\\DemoPlugin\\Providers\\CmdWordServiceProvider"
    ]
}
```

## Mapping

In the properties of the command word provider file `/plugins/DemoPlugin/Providers/CmdWordServiceProvider.php`, in `$cmdWordsMap`, add the command word mapping config.

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

## Output

### Output on successful processing

```php
public function sendCode($wordBody)
{
    <...>

    return [
        'code' => 0, // Status Code, success is 0
        'message' => 'success',
        'data' => [
            // Processing result data
        ]
    ];
}
```

### Output on processing failure

```php
public function sendCode($wordBody)
{
    <...>

    return [
        'code' => 21005, // Error Code
        'message' => 'Command word does not exist', // Error Description
        'data' => [
            // Wrong data
        ]
    ];
}
```

## Using

Each time you change the `plugin.json` configuration information, you will need to restart the plugin to apply the latest configuration.
