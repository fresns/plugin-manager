# 介紹

數據傳輸對象（Data Transfer Object）擴充包，用於輕鬆生成數據規則和檢查傳輸數據。與 Laravel 項目一起工作。

- [https://github.com/fresns/dto](https://github.com/fresns/dto)

## 安裝

```sh
composer require fresns/dto
```

## 產生 DTO

- 產生一個新的 DTO（路徑：`/app/DTO/`）

```sh
php artisan make:dto BaseDTO
```

- 產生一個新的 DTO（到指定路徑）

```sh
php artisan make:dto BaseDTO --path /app/Fresns/Panel
```

## 參考

請參閱 Laravel 文件，了解可用於驗證的規則。

- [https://laravel.com/docs/9.x/validation#available-validation-rules](https://laravel.com/docs/9.x/validation#available-validation-rules)
- [https://laravel.com/docs/10.x/validation#available-validation-rules](https://laravel.com/docs/10.x/validation#available-validation-rules)
- [https://laravel.com/docs/11.x/validation#available-validation-rules](https://laravel.com/docs/11.x/validation#available-validation-rules)
- [https://laravel.com/docs/12.x/validation#available-validation-rules](https://laravel.com/docs/12.x/validation#available-validation-rules)
- [https://laravel.com/docs/13.x/validation#available-validation-rules](https://laravel.com/docs/13.x/validation#available-validation-rules)
