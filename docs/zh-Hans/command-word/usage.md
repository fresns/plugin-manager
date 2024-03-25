# 命令字使用

插件与主程序之间，插件与插件之间，均通过[命令字](https://github.com/fresns/cmd-word-manager)通讯，一个完整的通讯流程包括请求输入 `input` 和结果输出 `output`。

## 请求输入 input

| 名称 | 说明 |
| --- | --- |
| `\FresnsCmdWord` | 命令字立面（Facades） |
| `FresnsEmail` | 请求对象 `fskey`，留空或填 `Fresns` 则表示由主程序处理请求 |
| `sendEmail` | 命令字（可参考 [Fresns 命令字](https://zh-hans.fresns.org/supports/cmd-word/basic.html)） |
| `$wordBody` | 命令字传参的参数列表 |

```php
// $参数数组名 = [参数数组];
$wordBody = [
    'email' => '收件地址',
    'title' => '邮件标题',
    'content' => '邮件内容',
];

// \命令字立面::plugin('插件名')->命令字($参数数组名)
\FresnsCmdWord::plugin('FresnsEmail')->sendEmail($wordBody);
```

**另一种写法**

```php
\FresnsCmdWord::plugin('FresnsEmail')->sendEmail([
    'email' => '收件地址',
    'title' => '邮件标题',
    'content' => '邮件内容',
]);
```

::: details 可选方法
| 名称 | 说明 |
| --- | --- |
| `$FsDto` | 数据传输对象：通过 Fresns DTO Contract 为你完成入参的数据校验，以及定义入参的调用方式（FsDto 名可自定义）。 |
| `make([...])` | `...` 为请求参数的数组 |
| `array` | 返参对象的契约，可以不使用，使用则是定义返参对象的契约。<br>比如自己开发的返参类型的约束功能，封装后在此定义使用；也可以使用官方提供的契约。 |
:::

::: details 可选方法示例
```php
//$参数列表 = 数据传输对象::make(参数数组);
$wordBody = FsDto::make([
    "email" => "收件地址",
    "title" => "邮件标题",
    "content" => "邮件内容"
]);

// \命令字立面::plugin('插件名')->命令字($参数列表): 定义返参对象的契约
\FresnsCmdWord::plugin('FresnsEmail')->sendEmail($wordBody);
```

**另一种写法**
```php
\FresnsCmdWord::plugin('FresnsEmail')->sendEmail(
    FsDto::make([
        "email" => "收件地址",
        "title" => "邮件标题",
        "content" => "邮件内容"
    ])
);
```
:::

## 结果输出 output

| 参数 | 说明 |
| --- | --- |
| code | 状态码 |
| message | 状态信息 |
| data | 输出数据 |

```json
// 成功
{
    "code": 0,
    "message": "ok",
    "data": {
        //命令字输出数据
    }
}

// 失败
{
    "code": 21001,
    "message": "插件不存在",
    "data": {
        //命令字输出数据
    }
}
```

## 错误码 error code

| Code | Message |
| --- | --- |
| 21000 | 未配置插件 |
| 21001 | 插件不存在 |
| 21002 | 命令字不存在 |
| 21003 | 命令字未知错误 |
| 21004 | 命令字无响应 |
| 21005 | 命令字请求参数错误 |
| 21006 | 命令字执行请求出错 |
| 21007 | 命令字响应结果不正确 |
| 21008 | 数据异常，查询不到或者数据重复 |
| 21009 | 执行异常，文件丢失或者记录错误 |
| 21010 | 命令字功能已关闭 |
| 21011 | 命令字配置不正确 |

## 结果处理 fresnsResp

如果你是标准的使用命令字返参结果，可以借助 Fresns Response 帮助你快速处理请求的返参。

**示例：**
```php
$fresnsResp = \FresnsCmdWord::plugin('FresnsEmail')->sendEmail($wordBody);
```

**处理异常情况**
```php
if ($fresnsResp->isErrorResponse()) {
    return $fresnsResp->getErrorResponse();
}
```

**处理正常情况**
```php
$fresnsResp->getOrigin(); //获取原始数据(code+message+data)

$fresnsResp->getCode(); //只获取 code
$fresnsResp->getMessage(); //只获取 message
$fresnsResp->getData(); //只获取 data 全量数据
$fresnsResp->getData('user.nickname'); //只获取 data 中指定参数，比如 data.user.nickname

$fresnsResp->isSuccessResponse(); //判断请求是否为 true
$fresnsResp->isErrorResponse(); //判断请求是否为 false

$fresnsResp->getErrorResponse(); //内部使用输出原始数据，API 调用输出 JSON
```
