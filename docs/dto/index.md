# Introduction

**Data Transfer Object** extension package for easy generation of data rules and inspection of transfer data. Works with **Laravel** projects.

- [https://github.com/fresns/dto](https://github.com/fresns/dto)

## Installation

To install through Composer, by run the following command:

```bash
composer require fresns/dto
```

## Generate DTO

- Generate a new DTO (path: `/app/DTO/`)

```php
php artisan make:dto BaseDTO
```

- Generate a new DTO (to the specified path)

```php
php artisan make:dto BaseDTO --path /app/Fresns/Panel
```

## Reference

See the Laravel documentation for the rules that can be used for validation.

- [https://laravel.com/docs/9.x/validation#available-validation-rules](https://laravel.com/docs/9.x/validation#available-validation-rules)
- [https://laravel.com/docs/10.x/validation#available-validation-rules](https://laravel.com/docs/10.x/validation#available-validation-rules)
- [https://laravel.com/docs/11.x/validation#available-validation-rules](https://laravel.com/docs/11.x/validation#available-validation-rules)
