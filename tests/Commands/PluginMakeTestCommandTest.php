<?php

namespace Fresns\PluginManager\Tests\Commands;

use Illuminate\Filesystem\Filesystem;
use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\Tests\TestCase;
use Illuminate\Support\Str;

class PluginMakeTestCommandTest extends TestCase
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

    public function test_it_generates_a_feature_test_file()
    {
        $code = $this->artisan('plugin:make-test', ['name' => 'FooBarFeature', 'plugin' => 'Blog', '--feature' => true]);

        $this->assertFileExists($filepath = $this->pluginPath.'/Tests/Feature/FooBarFeature.php');


        $this->assertTrue(Str::contains(file_get_contents($filepath), 'namespace Plugins\Blog\Tests\Feature;'));
        $this->assertTrue(Str::contains(file_get_contents($filepath), 'class FooBarFeature'));
        $this->assertTrue(Str::contains(file_get_contents($filepath), 'A basic feature test example.'));
        $this->assertSame(0, $code);
    }

    public function test_it_generates_a_unit_test_file()
    {
        $code = $this->artisan('plugin:make-test', ['name' => 'FooBarUnit', 'plugin' => 'Blog']);

        $this->assertFileExists($filepath = $this->pluginPath.'/Tests/Unit/FooBarUnit.php');


        $this->assertTrue(Str::contains(file_get_contents($filepath), 'namespace Plugins\Blog\Tests\Unit;'));
        $this->assertTrue(Str::contains(file_get_contents($filepath), 'class FooBarUnit'));
        $this->assertTrue(Str::contains(file_get_contents($filepath), 'A basic unit test example.'));
        $this->assertSame(0, $code);
    }
}
