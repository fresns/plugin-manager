---
layout: home

title: Plugin Manager
titleTemplate: 让 Laravel 应用更具组织性和可扩展性

hero:
  name: Plugin Manager
  text: 让 Laravel 应用更具组织性和可扩展性
  tagline: 插件管理器是一个便捷的 Laravel 扩展包，用于模块化管理您的庞大 Laravel 应用程序。每个插件就像一个独立的 Laravel 应用或者微服务，可以定义自己的视图、控制器和模型。
  image:
      src: https://files.fresns.org/wiki/icons/pm.png
      alt: PluginManager
  actions:
    - theme: brand
      text: 开始使用
      link: /zh-Hans/guide/
    - theme: alt
      text: 插件指令
      link: /zh-Hans/artisan/
    - theme: alt
      text: 命令字管理器
      link: /zh-Hans/command-word/

features:
  - icon: 🔌
    title: 即插即用
    details: 以服务提供者形式注册和挂载插件，即插即用，便于解耦。
  - icon: 🛠️
    title: 快捷开发
    details: 具备开发、控制、管理等指令，可以方便快捷的在命令行构建插件。
  - icon: 🕹
    title: 轻松管理
    details: 通过事件监听的方式执行插件的安装、卸载、启用和禁用等操作管理。
  - icon: ⚙️
    title: 独立依赖
    details: 每个插件有自己的 Composer 配置，不与主程序耦合，易于管理，灵活开发。
  - icon: 📡
    title: 互相调用
    details: 通过命令字管理器的协助，插件与主程序之间，插件与插件之间均可互相调用彼此的功能。
  - icon: 💡
    title: 生态支持
    details: 可以直接使用 Fresns 应用生态的插件，也可以构建自己的应用生态，灵活自由的发挥心中所想。
---
