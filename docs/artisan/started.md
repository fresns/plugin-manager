# Usage Flow

When using plug-in instructions, you need to enable the development mode first, then enter the plug-in directory, and directly use the instructions in the plug-in directory.

## 1. Enable development mode

```php
php artisan fresns
```

## 2. Introduce the project path (auto-identify, just enter)

```php
export /path/to/project/vendor/bin
```

## 3. Go to the plugin directory

- Create a plugin called `DemoPlugin`

```php
fresns new DemoPlugin
```

- Go to the plugin `DemoPlugin` directory

```php
fresns enter DemoPlugin
```

- Back to the fresns root directory

```php
fresns back
```

## 4. Execute development

Execute development, management, and control commands in the plugin directory
