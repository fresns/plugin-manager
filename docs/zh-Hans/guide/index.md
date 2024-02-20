# 介绍

<p align="center"><img src="https://assets.fresns.com/images/icons/pm.png" width="150"></p>

<p align="center">
<img src="https://img.shields.io/badge/PHP-%5E8.0-blueviolet" alt="PHP" style="display:inline;">
<img src="https://img.shields.io/badge/Laravel-9.x%7C10.x-orange" alt="Laravel" style="display:inline;margin:0 8px;">
<img src="https://img.shields.io/badge/License-Apache--2.0-green" alt="License" style="display:inline;">
</p>

Plugin Manager 是一个便捷的 Laravel 扩展包，用于模块化管理您的庞大 Laravel 应用程序。每个插件就像一个独立的 Laravel 应用或者微服务，可以定义自己的视图、控制器和模型。

## 特征

### 即插即用

以服务提供者形式注册和挂载插件，即插即用，便于解耦。

### 快捷开发

具备开发、控制、管理等指令，可以方便快捷的在命令行构建插件。

### 轻松管理

通过事件监听的方式执行插件的安装、卸载、启用和禁用等操作管理。

### 独立依赖

每个插件有自己的 Composer 配置，不与主程序耦合，易于管理，灵活开发。

### 互相调用

通过命令字管理器的协助，插件与主程序之间，插件与插件之间均可互相调用彼此的功能。

### 生态支持

可以直接使用 Fresns 应用生态的插件，也可以构建自己的应用生态，灵活自由的发挥心中所想。

## 使用案例

Plugin Manager 已经使用在 Fresns 社区程序中，并且构建了一个 Fresns 应用生态。

Fresns 是一款免费开源的社交网络服务软件，专为跨平台而打造的通用型社区产品，支持灵活多样的内容形态，可以满足多种运营场景，符合时代潮流，更开放且更易于二次开发。

- Fresns 官网 [https://zh-hans.fresns.org](https://zh-hans.fresns.org/)
- Fresns 应用市场 [https://marketplace.fresns.com](https://marketplace.fresns.com/zh-Hans/open-source)

## 代码仓库

- [https://github.com/fresns/plugin-manager](https://github.com/fresns/plugin-manager)

## 许可协议

Plugin Manager 遵循 [Apache-2.0](https://github.com/fresns/plugin-manager/blob/2.x/LICENSE) 开源协议。
