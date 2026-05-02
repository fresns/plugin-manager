# 介绍

数据传输对象（Data Transfer Object）扩展包，用于轻松生成数据规则和检查传输数据。与 Laravel 项目一起工作。

- [https://github.com/fresns/dto](https://github.com/fresns/dto)

## 安装

```sh
composer require fresns/dto
```

## 生成 DTO

- 生成一个新的 DTO（路径：`/app/DTO/`）

```sh
php artisan make:dto BaseDTO
```

- 生成一个新的 DTO（到指定路径）

```sh
php artisan make:dto BaseDTO --path /app/Fresns/Panel
```

## 参考

请参阅 Laravel 文档，了解可用于验证的规则。

- [https://laravel.com/docs/9.x/validation#available-validation-rules](https://laravel.com/docs/9.x/validation#available-validation-rules)
- [https://laravel.com/docs/10.x/validation#available-validation-rules](https://laravel.com/docs/10.x/validation#available-validation-rules)
- [https://laravel.com/docs/11.x/validation#available-validation-rules](https://laravel.com/docs/11.x/validation#available-validation-rules)
- [https://laravel.com/docs/12.x/validation#available-validation-rules](https://laravel.com/docs/12.x/validation#available-validation-rules)
- [https://laravel.com/docs/13.x/validation#available-validation-rules](https://laravel.com/docs/13.x/validation#available-validation-rules)
