# 总览

## 使用指令

```php
php artisan fresns                  // 进入插件开发模式

fresns plugin                       // 查看所有可用指令
fresns plugin:list                  // 查看所有已安装插件
fresns new                          // 创建新插件
fresns enter                        // 进入指定插件目录
fresns back                         // 回到项目根目录
```

## 开发指令

```php
fresns make:command                 // 生成插件 Command
fresns make:migration               // 生成插件 Migration
fresns make:seed                    // 生成插件 Seed
fresns make:factory                 // 生成插件 Factory
fresns make:provider                // 生成插件 Provider
fresns make:controller              // 生成插件 Controller
fresns make:model                   // 生成插件 Model
fresns make:middleware              // 生成插件 Middleware
fresns make:dto                     // 生成插件 DTO (fresns/dto)
fresns make:mail                    // 生成插件 Mail
fresns make:notification            // 生成插件 Notification
fresns make:listener                // 生成插件 Listener
fresns make:request                 // 生成插件 Request
fresns make:event                   // 生成插件 Event
fresns make:job                     // 生成插件 Job
fresns make:policy                  // 生成插件 Policy
fresns make:rule                    // 生成插件 Rule
fresns make:resource                // 生成插件 Resource
fresns make:test                    // 生成插件 Test
fresns make:schedule-provider       // 生成插件任务调度提供者
fresns make:cmd-word-provider       // 生成插件命令字提供者 (fresns/cmd-word-manager)
fresns make:event-provider          // 生成插件事件服务提供者
```

## 控制指令

### fresns 模式

```php
fresns plugin:unzip                 // 解压插件包到插件目录 /extensions/plugins/{unikey}/
fresns plugin:publish               // 发布插件（分发静态资源） /public/assets/plugins/{unikey}/
fresns plugin:unpublish             // 撤销发布（删除静态资源）
fresns plugin:composer-update       // 更新插件 Composer 依赖包
fresns plugin:migrate               // 执行插件 Migrate
fresns plugin:migrate-rollback      // 回滚插件 Migrate
fresns plugin:migrate-refresh       // 刷新插件 Migrate
fresns plugin:migrate-reset         // 重置插件 Migrate
fresns plugin:seed                  // 执行插件 Seed
fresns plugin:install               // 安装插件（逐个执行 unzip/publish/composer-update/migrate 指令）
fresns plugin:uninstall             // 卸载插件
```

### artisan 模式

```php
php artisan plugin:unzip            // 解压插件包到插件目录 /extensions/plugins/{unikey}/
php artisan plugin:publish          // 发布插件（分发静态资源） /public/assets/plugins/{unikey}/
php artisan plugin:unpublish        // 撤销发布（删除静态资源）
php artisan plugin:composer-update  // 更新插件 Composer 依赖包
php artisan plugin:migrate          // 执行插件 Migrate
php artisan plugin:migrate-rollback // 回滚插件 Migrate
php artisan plugin:migrate-refresh  // 刷新插件 Migrate
php artisan plugin:migrate-reset    // 重置插件 Migrate
php artisan plugin:seed             // 执行插件 Seed
php artisan plugin:install          // 安装插件（逐个执行 unzip/publish/composer-update/migrate 指令）
php artisan plugin:uninstall        // 卸载插件
```

## 管理指令

### fresns 模式

```php
fresns plugin:activate              // 启用插件
fresns plugin:deactivate            // 停用插件
```

### artisan 模式

```php
php artisan plugin:activate         // 启用插件
php artisan plugin:deactivate       // 停用插件
```
