# Command Word Usage

[Command word manager](https://github.com/fresns/cmd-word-manager)(in laravel) helps plugins(individual functional modules) to communicate with each other easily.

## Request Input

| Name | Description |
| --- | --- |
| `\FresnsCmdWord` | Command Word Facades |
| `FresnsEmail` | Requesting Object `fskey`, Leaving blank or filling in `Fresns` means that the main program handles the request |
| `sendEmail` | Command Word([Reference Fresns command word](https://fresns.org/supports/cmd-word/basic.html)) |
| `$wordBody` | Parameter list of command word parameters |

```php
// $parameter list = (parameter array);
$wordBody = [
    'email' => 'Mail address',
    'title' => 'Mail title',
    'content' => 'Mail content',
];

// \facades::plugin('plugin name')->cmd word($parameter list): Define the contract for the return object
\FresnsCmdWord::plugin('FresnsEmail')->sendEmail($wordBody);
```

**Another way to write**

```php
\FresnsCmdWord::plugin('FresnsEmail')->sendEmail([
    'email' => 'Mail address',
    'title' => 'Mail title',
    'content' => 'Mail content',
]);
```

## Request Output

| Parameter | Description |
| --- | --- |
| code | Status code |
| message | Status information |
| data | Output data |

```json
// Success
{
    "code": 0,
    "message": "ok",
    "data": {
        // Command word output data
    }
}

// Failure
{
    "code": 21001,
    "message": "Plugin does not exist",
    "data": {
        // Command word output data
    }
}
```

## Error Code

| Code | Message |
| --- | --- |
| 21000 | Unconfigured plugin |
| 21001 | Plugin does not exist |
| 21002 | Command word does not exist |
| 21003 | Command word unknown error |
| 21004 | Command word not responding |
| 21005 | Command word request parameter error |
| 21006 | Command word execution request error |
| 21007 | Command word response result is incorrect |
| 21008 | Data anomalies, queries not available or data duplication |
| 21009 | Execution anomalies, missing files or logging errors |

## Response

If you are standardized to use command word return results, you can use Fresns Response to help you quickly handle the return of the request.

**Example:**
```php
$fresnsResp = \FresnsCmdWord::plugin('FresnsEmail')->sendEmail($wordBody);
```

**Handling abnormal situations**
```php
if ($fresnsResp->isErrorResponse()) {
    return $fresnsResp->errorResponse(); // When an error is reported, the full amount of parameters is output(code+message+data)
}
```

**Handling normal situations**
```php
$fresnsResp->getOrigin(); // Obtaining raw data
$fresnsResp->getCode(); // Get code only
$fresnsResp->getMessage(); // Get only the message
$fresnsResp->getData(); // Get only the full amount of data
$fresnsResp->getData('user.nickname'); // Get only the parameters specified in data, for example: data.user.nickname
$fresnsResp->isSuccessResponse(); // Determine if the request is true
$fresnsResp->isErrorResponse(); // Determine if the request is false
$fresnsResp->getErrorResponse(); // Get the error response object
```
