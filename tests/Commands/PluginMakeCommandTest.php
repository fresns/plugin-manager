<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Tests\Commands;

use Fresns\PluginManager\Contracts\ActivatorInterface;
use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class PluginMakeCommandTest extends TestCase
{
    private Filesystem $finder;

    private string $pluginPath;

    private ActivatorInterface $activator;

    private RepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->finder = $this->app['files'];
        $this->repository = $this->app[RepositoryInterface::class];
        $this->activator = $this->app[ActivatorInterface::class];
        $this->pluginPath = $this->repository->getPluginDirectoryPath('Blog');
    }

    public function tearDown(): void
    {
        $this->repository->deletePluginDirectory(pathinfo($this->pluginPath, PATHINFO_FILENAME));
        $this->activator->reset();
        parent::tearDown();
    }

    public function test_it_generates_plugin()
    {
        $code = $this->artisan('plugin:make', ['name' => ['PluginName']]);
        $this->assertDirectoryExists($path = $this->repository->getPluginDirectoryPath('PluginName'));

        $this->repository->deletePluginDirectory('PluginName');
        $this->assertDirectoryNotExists($path);

        $this->assertSame(0, $code);
    }

    public function test_it_generates_plugin_folders()
    {
        $code = $this->artisan('plugin:make', ['name' => ['Blog']]);

        foreach (config('plugins.paths.generator') as $directory) {
            if (!($directory['generate'] ?? false)) {
                continue;
            }

            $this->assertDirectoryExists($this->pluginPath.'/'.$directory['path']);
        }

        $this->repository->deletePluginDirectory('Blog');
        $this->assertDirectoryNotExists($this->repository->getPluginDirectoryPath('Blog'));

        $this->assertSame(0, $code);
    }

    public function test_it_generates_plugin_files()
    {
        $code = $this->artisan('plugin:make', ['name' => ['Blog']]);

        foreach (config('plugins.stubs.files') as $file) {
            $path = base_path('plugins/Blog').'/'.$file;
            $this->assertTrue($this->finder->exists($path), "[$file] does not exists");
        }

        $path = base_path('plugins/Blog').'/plugin.json';

        $this->assertTrue($this->finder->exists($path), '[plugin.json] does not exists');

        $this->repository->deletePluginDirectory('Blog');
        $this->assertDirectoryNotExists($this->repository->getPluginDirectoryPath('Blog'));
        $this->assertSame(0, $code);
    }

    public function test_it_generates_plugin_resources()
    {
        $code = $this->artisan('plugin:make', ['name' => ['Blog']]);

        $path = base_path('plugins/Blog').'/Providers/BlogServiceProvider.php';
        $this->assertTrue($this->finder->exists($path));

        $path = base_path('plugins/Blog').'/Http/Controllers/BlogController.php';
        $this->assertTrue($this->finder->exists($path));

        $path = base_path('plugins/Blog').'/Database/Seeders/BlogDatabaseSeeder.php';
        $this->assertTrue($this->finder->exists($path));

        $path = base_path('plugins/Blog').'/Providers/RouteServiceProvider.php';
        $this->assertTrue($this->finder->exists($path));

        $this->repository->deletePluginDirectory('Blog');
        $this->assertDirectoryNotExists($this->repository->getPluginDirectoryPath('Blog'));

        $this->assertSame(0, $code);
    }

    public function test_it_generates_plugin_folder_using_studly_case()
    {
        $code = $this->artisan('plugin:make', ['name' => ['PluginName']]);

        $this->assertDirectoryExists($path = $this->repository->getPluginDirectoryPath('PluginName'));

        $this->repository->deletePluginDirectory(pathinfo($path, PATHINFO_BASENAME));
        $this->assertDirectoryNotExists($path);

        $this->assertSame(0, $code);
    }

    public function test_it_outputs_error_when_plugin_exists()
    {
        $this->artisan('plugin:make', ['name' => ['Blog']]);
        $this->assertDirectoryExists($path = $this->repository->getPluginDirectoryPath('Blog'));

        $code = $this->artisan('plugin:make', ['name' => ['Blog']]);

        $expected = 'Plugin [Blog] already exist!
';

        $this->assertEquals($expected, Artisan::output());

        $this->repository->deletePluginDirectory(pathinfo($path, PATHINFO_BASENAME));
        $this->assertDirectoryNotExists($path);

        $this->assertSame(E_ERROR, $code);
    }

    public function test_it_still_generates_plugin_if_it_exists_using_force_flag()
    {
        $this->artisan('plugin:make', ['name' => ['Blog']]);
        $this->assertDirectoryExists($path = $this->repository->getPluginDirectoryPath('Blog'));

        $code = $this->artisan('plugin:make', ['name' => ['Blog'], '--force' => true]);

        $output = Artisan::output();

        $notExpected = 'Plugin [Blog] already exist!';

        $this->assertNotEquals($notExpected, $output);
        $this->assertTrue(Str::contains($output, 'Plugin [Blog] created successfully.'));

        $this->repository->deletePluginDirectory(pathinfo($path, PATHINFO_BASENAME));
        $this->assertDirectoryNotExists($path);

        $this->assertSame(0, $code);
    }

    public function test_it_can_generate_plugin_with_old_config_format()
    {
        $this->app['config']->set('plugins.paths.generator', [
            'assets' => 'Assets',
            'config' => 'Config',
            'command' => 'Console',
            'event' => 'Events',
            'listener' => 'Listeners',
            'migration' => 'Database/Migrations',
            'factory' => 'Database/factories',
            'model' => 'Entities',
            'repository' => 'Repositories',
            'seeder' => 'Database/Seeders',
            'controller' => 'Http/Controllers',
            'filter' => 'Http/Middleware',
            'request' => 'Http/Requests',
            'provider' => 'Providers',
            'lang' => 'Resources/lang',
            'views' => 'Resources/views',
            'policies' => false,
            'rules' => false,
            'test' => 'Tests',
            'jobs' => 'Jobs',
            'emails' => 'Emails',
            'notifications' => 'Notifications',
            'resource' => false,
        ]);

        $code = $this->artisan('plugin:make', ['name' => ['Blog']]);

        $this->assertDirectoryExists($this->pluginPath.'/Assets');
        $this->assertDirectoryExists($this->pluginPath.'/Emails');
        $this->assertDirectoryNotExists($this->pluginPath.'/Rules');
        $this->assertDirectoryNotExists($this->pluginPath.'/Policies');

        $this->assertDirectoryExists($path = $this->repository->getPluginDirectoryPath('Blog'));
        $this->repository->deletePluginDirectory(pathinfo($path, PATHINFO_BASENAME));
        $this->assertDirectoryNotExists($path);

        $this->assertSame(0, $code);
    }

    public function test_it_can_ignore_some_folders_to_generate_with_old_format()
    {
        $this->app['config']->set('plugins.paths.generator.assets', false);
        $this->app['config']->set('plugins.paths.generator.emails', false);

        $code = $this->artisan('plugin:make', ['name' => ['Blog']]);

        $this->assertDirectoryNotExists($this->pluginPath.'/Assets');
        $this->assertDirectoryNotExists($this->pluginPath.'/Emails');

        $this->assertDirectoryExists($path = $this->repository->getPluginDirectoryPath('Blog'));
        $this->repository->deletePluginDirectory(pathinfo($path, PATHINFO_BASENAME));
        $this->assertDirectoryNotExists($path);

        $this->assertSame(0, $code);
    }

    public function test_it_can_ignore_some_folders_to_generate_with_new_format()
    {
        $this->app['config']->set('plugins.paths.generator.assets', ['path' => 'Assets', 'generate' => false]);
        $this->app['config']->set('plugins.paths.generator.emails', ['path' => 'Emails', 'generate' => false]);

        $code = $this->artisan('plugin:make', ['name' => ['Blog']]);

        $this->assertDirectoryNotExists($this->pluginPath.'/Assets');
        $this->assertDirectoryNotExists($this->pluginPath.'/Emails');

        $this->assertDirectoryExists($path = $this->repository->getPluginDirectoryPath('Blog'));
        $this->repository->deletePluginDirectory(pathinfo($path, PATHINFO_BASENAME));
        $this->assertDirectoryNotExists($path);

        $this->assertSame(0, $code);
    }

    public function test_it_can_ignore_resource_folders_to_generate()
    {
        $this->app['config']->set('plugins.paths.generator.seeder', ['path' => 'Database/Seeders', 'generate' => false]);
        $this->app['config']->set('plugins.paths.generator.provider', ['path' => 'Providers', 'generate' => false]);
        $this->app['config']->set('plugins.paths.generator.controller', ['path' => 'Http/Controllers', 'generate' => false]);

        $code = $this->artisan('plugin:make', ['name' => ['Blog']]);

        $this->assertDirectoryNotExists($this->pluginPath.'/Database/Seeders');
        $this->assertDirectoryNotExists($this->pluginPath.'/Providers');
        $this->assertDirectoryNotExists($this->pluginPath.'/Http/Controllers');

        $this->assertDirectoryExists($path = $this->repository->getPluginDirectoryPath('Blog'));
        $this->repository->deletePluginDirectory(pathinfo($path, PATHINFO_BASENAME));
        $this->assertDirectoryNotExists($path);

        $this->assertSame(0, $code);
    }

    public function test_it_generates_enabled_plugin()
    {
        $code = $this->artisan('plugin:make', ['name' => ['Blog']]);

        $this->assertTrue($this->repository->isEnabled('Blog'));

        $this->assertDirectoryExists($path = $this->repository->getPluginDirectoryPath('Blog'));
        $this->repository->deletePluginDirectory(pathinfo($path, PATHINFO_BASENAME));
        $this->assertDirectoryNotExists($path);

        $this->assertSame(0, $code);
    }

    public function test_it_generates_disabled_plugin_with_disabled_flag()
    {
        $code = $this->artisan('plugin:make', ['name' => ['Blog'], '--disabled' => true]);

        $this->assertTrue($this->repository->isDisabled('Blog'));

        $this->assertDirectoryExists($path = $this->repository->getPluginDirectoryPath('Blog'));
        $this->repository->deletePluginDirectory(pathinfo($path, PATHINFO_BASENAME));
        $this->assertDirectoryNotExists($path);

        $this->assertSame(0, $code);
    }
}
