# Overview

## Usage

```php
php artisan fresns                  // Enter Plugin Development Mode

fresns plugin                       // View All Commands
fresns plugin:list                  // View All Installed Plugins
fresns new                          // Generate A New Plugin
fresns enter                        // Go to plugin directory
fresns back                         // Back to the fresns root directory
```

## Development

```php
fresns make:command                 // Generate Plugin Command
fresns make:migration               // Generate Plugin Migration
fresns make:seed                    // Generate Plugin Seed
fresns make:factory                 // Generate Plugin Factory
fresns make:provider                // Generate Plugin Provider
fresns make:controller              // Generate Plugin Controller
fresns make:model                   // Generate Plugin Model
fresns make:middleware              // Generate Plugin Middleware
fresns make:dto                     // Generate Plugin DTO (fresns/dto)
fresns make:mail                    // Generate Plugin Mail
fresns make:notification            // Generate Plugin Notification
fresns make:listener                // Generate Plugin Listener
fresns make:request                 // Generate Plugin Request
fresns make:event                   // Generate Plugin Event
fresns make:job                     // Generate Plugin Job
fresns make:policy                  // Generate Plugin Policy
fresns make:rule                    // Generate Plugin Rule
fresns make:resource                // Generate Plugin Resource
fresns make:test                    // Generate Plugin Test
fresns make:schedule-provider       // Generate Plugin Schedule Provider
fresns make:event-provider          // Generate Plugin Event Provider
fresns make:sql-provider            // Generate Plugin SQL Provider
fresns make:cmdword-provider        // Generate Plugin Command Word Provider (fresns/cmd-word-manager)
```

## Control

### fresns mode

```php
fresns plugin:unzip                 // Unzip the plugin package to the plugin directory: /extensions/plugins/{fskey}/
fresns plugin:publish               // Publish Plugin (static resources): /public/assets/plugins/{fskey}/
fresns plugin:unpublish             // Unpublish (remove static resources)
fresns plugin:composer-update       // Update Plugin Composer Package
fresns plugin:migrate               // Run Plugin Migrate
fresns plugin:migrate-rollback      // Rollback Plugin Migrate
fresns plugin:migrate-refresh       // Refresh Plugin Migrate
fresns plugin:migrate-reset         // Reset Plugin Migrate
fresns plugin:seed                  // Run Plugin Seed
fresns plugin:install               // Install Plugin (Run the unzip/publish/composer-update/migrate command in sequence)
fresns plugin:uninstall             // Uninstall Plugin
```

### artisan mode

```php
php artisan plugin:unzip            // Unzip the plugin package to the plugin directory: /extensions/plugins/{fskey}/
php artisan plugin:publish          // Publish Plugin (static resources): /public/assets/plugins/{fskey}/
php artisan plugin:unpublish        // Unpublish (remove static resources)
php artisan plugin:composer-update  // Update Plugin Composer Package
php artisan plugin:migrate          // Run Plugin Migrate
php artisan plugin:migrate-rollback // Rollback Plugin Migrate
php artisan plugin:migrate-refresh  // Refresh Plugin Migrate
php artisan plugin:migrate-reset    // Reset Plugin Migrate
php artisan plugin:seed             // Run Plugin Seed
php artisan plugin:install          // Install Plugin (Run the unzip/publish/composer-update/migrate command in sequence)
php artisan plugin:uninstall        // Uninstall Plugin
```

## Management

### fresns mode

```php
php artisan plugin:activate         // Activate Plugin
php artisan plugin:deactivate       // Deactivate Plugin
```

### artisan mode

```php
fresns plugin:activate              // Activate Plugin
fresns plugin:deactivate            // Deactivate Plugin
```
