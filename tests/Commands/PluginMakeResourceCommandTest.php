<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Tests\Commands;

use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class PluginMakeResourceCommandTest extends TestCase
{
    /**
     * @var Filesystem
     */
    private Filesystem $finder;

    /**
     * @var string
     */
    private string  $pluginPath;

    public function setUp(): void
    {
        parent::setUp();

        $this->pluginPath = base_path('plugins/Blog');
        $this->finder = $this->app['files'];

        $this->artisan('plugin:make', ['name' => ['Blog']]);
    }

    public function tearDown(): void
    {
        $this->app[RepositoryInterface::class]->delete('Blog');
        $this->app[RepositoryInterface::class]->deletePluginDirectory('Blog');

        parent::tearDown();
    }

    public function test_it_generates_a_resource_file()
    {
        $code = $this->artisan('plugin:make-resource', ['name' => 'FooBarResource', 'plugin' => 'Blog']);

        $this->assertFileExists($filepath = $this->pluginPath.'/Http/Resources/FooBarResource.php');

        $this->assertTrue(Str::contains(file_get_contents($filepath), 'namespace Plugins\Blog\Http\Resources;'));
        $this->assertTrue(Str::contains(file_get_contents($filepath), 'class FooBarResource'));
        $this->assertTrue(Str::contains(file_get_contents($filepath), 'class FooBarResource extends JsonResource'));
        $this->assertSame(0, $code);
    }

    public function test_it_generates_a_resource_collection_file_by_c_flag()
    {
        $code = $this->artisan('plugin:make-resource', ['name' => 'FooBarResource', 'plugin' => 'Blog', '-c' => true]);

        $this->assertFileExists($filepath = $this->pluginPath.'/Http/Resources/FooBarResource.php');

        $this->assertTrue(Str::contains(file_get_contents($filepath), 'namespace Plugins\Blog\Http\Resources;'));
        $this->assertTrue(Str::contains(file_get_contents($filepath), 'class FooBarResource'));
        $this->assertTrue(Str::contains(file_get_contents($filepath), 'class FooBarResource extends ResourceCollection'));
        $this->assertSame(0, $code);
    }

    public function test_it_generates_a_resource_collection_file_by_collection_name()
    {
        $code = $this->artisan('plugin:make-resource', ['name' => 'FooBarResource', 'plugin' => 'Blog', '-c' => true]);

        $this->assertFileExists($filepath = $this->pluginPath.'/Http/Resources/FooBarResource.php');

        $this->assertTrue(Str::contains(file_get_contents($filepath), 'namespace Plugins\Blog\Http\Resources;'));
        $this->assertTrue(Str::contains(file_get_contents($filepath), 'class FooBarResource'));
        $this->assertTrue(Str::contains(file_get_contents($filepath), 'class FooBarResource extends ResourceCollection'));
        $this->assertSame(0, $code);
    }
}
