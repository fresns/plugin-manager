<?php

namespace Fresns\PluginManager\Tests\Commands;

use Illuminate\Filesystem\Filesystem;
use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\Tests\TestCase;

class PluginMakeControllerCommandTest extends TestCase
{
    private Filesystem $finder;

    private string $pluginPath;

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

    public function test_it_generates_a_new_controller_class()
    {
        $code = $this->artisan('plugin:make-controller', ['controller' => 'MyController', 'plugin' => 'Blog']);
        $this->assertFileExists($this->pluginPath.'/Http/Controllers/MyController.php');

        $fileCount = count($this->finder->files($this->pluginPath."/Database/Seeders/"));
        $this->assertGreaterThan(0, $fileCount, 'Create migration file failure.');

        $this->assertSame(0, $code);
    }

    public function test_it_appends_controller_to_name_if_not_present()
    {
        $code = $this->artisan('plugin:make-controller', ['controller' => 'My', 'plugin' => 'Blog']);
        $this->assertFileExists($this->pluginPath.'/Http/Controllers/MyController.php');
        $this->assertSame(0, $code);
    }
}
