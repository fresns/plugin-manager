<?php

namespace Fresns\PluginManager\Tests\Commands;

use Illuminate\Filesystem\Filesystem;
use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\Tests\TestCase;
use Illuminate\Support\Str;

class PluginMakeCommandCommandTest extends TestCase
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

    public function test_it_generates_a_command_file()
    {
        $code = $this->artisan('plugin:make-command', ['name' => 'FooBarCommand', 'plugin' => 'Blog']);

        $this->assertFileExists($this->pluginPath.'/Console/FooBarCommand.php');
        $this->assertSame(0, $code);
    }

    public function test_it_generates_a_command_file_and_set_command_name()
    {
        $code = $this->artisan('plugin:make-command', [
            'name' => 'BarBazCommand',
            'plugin' => 'Blog',
            '--command' => 'bar:baz',
        ]);

        $this->assertFileExists($this->pluginPath.'/Console/BarBazCommand.php');

        $contents = file_get_contents($this->pluginPath.'/Console/BarBazCommand.php');
        $this->assertTrue(Str::contains($contents, 'bar:baz'));

        $this->assertSame(0, $code);
    }
}
