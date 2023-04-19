# 介绍

Laravel 命令字管理器是一个实用的 Laravel 扩展包，旨在帮助插件（独立功能模块）之间轻松实现通信。通过使用此扩展包，您可以更高效地组织和管理插件之间的交互，提升整体应用的协同效果。

- [https://github.com/fresns/cmd-word-manager](https://github.com/fresns/cmd-word-manager)

## 安装

```bash
composer require fresns/cmd-word-manager
```

## 查看

查看所有注册的插件，以及插件注册的命令字。

```php
\FresnsCmdWord::all();
```
