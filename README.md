<p align="center"><a href="https://pm.fresns.org" target="_blank"><img src="https://files.fresns.org/wiki/icons/pm.png" width="150"></a></p>

<p align="center">
<img src="https://img.shields.io/badge/PHP-%5E8.0-blueviolet" alt="PHP">
<img src="https://img.shields.io/badge/Laravel-9.x%7C10.x-orange" alt="Laravel">
<img src="https://img.shields.io/badge/License-Apache--2.0-green" alt="License">
</p>

## About Plugin Manager

Enhance Laravel Apps: Organized & Scalable

`fresns/plugin-manager` is a convenient Laravel extension package designed for modular management of your large-scale Laravel applications. Each plugin acts as an independent Laravel application or microservice, allowing you to define your own views, controllers and models.

Plugin Manager Docs: [https://pm.fresns.org](https://pm.fresns.org/)

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

## Development Docs

[https://pm.fresns.org](https://pm.fresns.org/)

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/fresns/plugin-manager/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/fresns/plugin-manager/issues).
3. Contribute new features or update the wiki.

*The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable.*

## License

Fresns Plugin Manager is open-sourced software licensed under the [Apache-2.0 license](https://github.com/fresns/plugin-manager/blob/main/LICENSE).
