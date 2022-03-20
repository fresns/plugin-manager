<p align="center"><a href="https://fresns.cn" target="_blank"><img src="https://cdn.fresns.cn/images/logo.png" width="300"></a></p>

<p align="center">
<img src="https://img.shields.io/badge/PHP-%5E8.0-blue" alt="PHP">
<img src="https://img.shields.io/badge/License-Apache--2.0-green" alt="License">
</p>

## About Plugin Manager

`fresns/plugin-manager` is a Laravel package which created to manage your large Laravel app using modules. Module is like a Laravel package, it has some views, controllers or models. This package is supported and tested in Laravel 9.

## Install

To install through Composer, by run the following command:

```bash
composer require fresns/plugin-manager
```

The package will automatically register a service provider and alias.

Optionally, publish the package's configuration file by running:

```bash
php artisan vendor:publish --provider="Fresns\PluginManager\Providers\PluginServiceProvider"
```

## Dev Docs

[https://fresns.org/extensions/plugin/](https://fresns.org/extensions/plugin/)

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/fresns/plugin-manager/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/fresns/plugin-manager/issues).
3. Contribute new features or update the wiki.

*The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable.*

## License

Fresns Plugin Manager is open-sourced software licensed under the [Apache-2.0 license](https://github.com/fresns/plugin-manager/blob/main/LICENSE).