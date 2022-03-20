<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Providers;

use Carbon\Laravel\ServiceProvider;
use Fresns\PluginManager\Console\Commands\AppStoreDownloadCommand;
use Fresns\PluginManager\Console\Commands\DeveloperLoginCommand;
use Fresns\PluginManager\Console\Commands\DeveloperUploadPluginCommand;
use Fresns\PluginManager\Console\Commands\DeveloperUploadThemeCommand;
use Fresns\PluginManager\Console\Commands\PluginActivateCommand;
use Fresns\PluginManager\Console\Commands\PluginCommand;
use Fresns\PluginManager\Console\Commands\PluginComposerInstallCommand;
use Fresns\PluginManager\Console\Commands\PluginComposerRemoveCommand;
use Fresns\PluginManager\Console\Commands\PluginComposerRequireCommand;
use Fresns\PluginManager\Console\Commands\PluginDeactivateCommand;
use Fresns\PluginManager\Console\Commands\PluginInstallCommand;
use Fresns\PluginManager\Console\Commands\PluginListCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeCmdWordProviderCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeCommandCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeConsoleProviderCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeControllerCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeDTOCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeEventCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeEventProviderCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeFactoryCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeJobCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeListenerCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeMailCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeMiddlewareCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeMigrationCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeModelCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeNotificationCommand;
use Fresns\PluginManager\Console\Commands\PluginMakePolicyCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeProviderCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeRequestCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeResourceCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeRuleCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeSeedCommand;
use Fresns\PluginManager\Console\Commands\PluginMakeTestCommand;
use Fresns\PluginManager\Console\Commands\PluginMigrateCommand;
use Fresns\PluginManager\Console\Commands\PluginMigrateRefreshCommand;
use Fresns\PluginManager\Console\Commands\PluginMigrateResetCommand;
use Fresns\PluginManager\Console\Commands\PluginMigrateRollbackCommand;
use Fresns\PluginManager\Console\Commands\PluginPublishCommand;
use Fresns\PluginManager\Console\Commands\PluginRouteProviderCommand;
use Fresns\PluginManager\Console\Commands\PluginSeedCommand;
use Fresns\PluginManager\Console\Commands\PluginUninstallCommand;
use Fresns\PluginManager\Console\Commands\PluginUnpublishCommand;
use Fresns\PluginManager\Console\Commands\PluginUnzipCommand;
use Fresns\PluginManager\Console\Commands\ThemeActivateCommand;
use Fresns\PluginManager\Console\Commands\ThemeDeactivateCommand;
use Fresns\PluginManager\Console\Commands\ThemeInstallCommand;
use Fresns\PluginManager\Console\Commands\ThemePublishCommand;
use Fresns\PluginManager\Console\Commands\ThemeUninstallCommand;
use Fresns\PluginManager\Console\Commands\ThemeUnpublishCommand;
use Fresns\PluginManager\Console\Commands\ThemeUnzipCommand;
use Illuminate\Support\Str;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Namespace of the console commands.
     *
     * @var string
     */
    protected string $consoleNamespace = 'Fresns\\PluginManager\\Console\\Commands';

    /**
     * The available commands.
     *
     * @var array
     */
    protected array $commands = [
        PluginCommand::class,
        PluginListCommand::class,

        // Development commands
        PluginMakeCommand::class,
        PluginMakeCommandCommand::class,
        PluginMakeMigrationCommand::class,
        PluginMakeSeedCommand::class,
        PluginMakeFactoryCommand::class,
        PluginMakeProviderCommand::class,
        PluginMakeControllerCommand::class,
        PluginMakeModelCommand::class,
        PluginMakeMiddlewareCommand::class,
        PluginMakeDTOCommand::class,
        PluginMakeMailCommand::class,
        PluginMakeNotificationCommand::class,
        PluginMakeListenerCommand::class,
        PluginMakeRequestCommand::class,
        PluginMakeEventCommand::class,
        PluginMakeJobCommand::class,
        PluginMakePolicyCommand::class,
        PluginMakeRuleCommand::class,
        PluginMakeResourceCommand::class,
        PluginMakeTestCommand::class,
        PluginMakeConsoleProviderCommand::class,
        PluginMakeCmdWordProviderCommand::class,
        PluginMakeEventProviderCommand::class,
        PluginRouteProviderCommand::class,
        PluginComposerRequireCommand::class,
        PluginComposerRemoveCommand::class,

        // Control commands
        PluginUnzipCommand::class,
        PluginPublishCommand::class,
        PluginUnpublishCommand::class,
        PluginComposerInstallCommand::class,
        PluginMigrateCommand::class,
        PluginMigrateRollbackCommand::class,
        PluginMigrateRefreshCommand::class,
        PluginMigrateResetCommand::class,
        PluginSeedCommand::class,
        PluginUninstallCommand::class,
        ThemeUnzipCommand::class,
        ThemePublishCommand::class,
        ThemeUnpublishCommand::class,
        ThemeUninstallCommand::class,

        // Management commands
        PluginInstallCommand::class,
        PluginActivateCommand::class,
        PluginDeactivateCommand::class,
        ThemeInstallCommand::class,
        ThemeActivateCommand::class,
        ThemeDeactivateCommand::class,

        // Developer commands
        DeveloperLoginCommand::class,
        DeveloperUploadPluginCommand::class,
        DeveloperUploadThemeCommand::class,

        // Fresns app store commands
        AppStoreDownloadCommand::class,
    ];

    /**
     * @return array
     */
    private function resolveCommands(): array
    {
        $commands = [];

        foreach ((config('plugins.commands') ?: $this->commands) as $command) {
            $commands[] = Str::contains($command, $this->consoleNamespace) ?
                $command :
                $this->consoleNamespace.'\\'.$command;
        }

        return $commands;
    }

    public function register(): void
    {
        $this->commands($this->resolveCommands());
    }

    /**
     * @return array
     */
    public function provides(): array
    {
        return $this->commands;
    }
}
