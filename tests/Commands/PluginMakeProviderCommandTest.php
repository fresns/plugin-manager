<?php

namespace Fresns\PluginManager\Tests\Commands;

use Illuminate\Filesystem\Filesystem;
use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\Tests\TestCase;
use Illuminate\Support\Str;

class PluginMakeProviderCommandTest extends TestCase
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

    public function test_it_generates_a_service_provider()
    {
        $code = $this->artisan('plugin:make-provider', ['name' => 'MyBlogServiceProvider', 'plugin' => 'Blog']);
        $this->assertFileExists($this->pluginPath.'/Providers/MyBlogServiceProvider.php');
        $this->assertSame(0, $code);
    }

    public function test_it_generates_a_cmd_word_service_provider()
    {
        $code = $this->artisan('plugin:make-provider', ['name' => 'CmdWordServiceProvider', 'plugin' => 'Blog']);
        $content = file_get_contents($this->pluginPath.'/Providers/CmdWordServiceProvider.php');
        $contains = Str::contains($content, 'CmdWordServiceProvider');
        $this->assertTrue($contains);
        $this->assertFileExists($this->pluginPath.'/Providers/CmdWordServiceProvider.php');
        $this->assertSame(0, $code);
    }
}
