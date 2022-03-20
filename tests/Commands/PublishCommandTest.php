<?php

namespace Fresns\PluginManager\Tests\Commands;

use Illuminate\Filesystem\Filesystem;
use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\Support\Config\GenerateConfigReader;
use Fresns\PluginManager\Tests\TestCase;

class PublishCommandTest extends TestCase
{
    /**
     * @var Filesystem
     */
    private Filesystem $finder;
    /**
     * @var string
     */
    private string $pluginPath;

    public function setUp(): void
    {
        parent::setUp();
        $this->pluginPath = base_path('plugins/Blog');
        $this->finder = $this->app['files'];
        $this->artisan('plugin:make', ['name' => ['Blog']]);
        $this->finder->put($this->pluginPath.'/'.GenerateConfigReader::read('assets')->getPath().'/script.js', 'assetfile');
    }

    public function tearDown(): void
    {
        $this->app[RepositoryInterface::class]->delete('Blog');
        $this->app[RepositoryInterface::class]->deletePluginDirectory('Blog');
        parent::tearDown();
    }

    /** @test */
    public function it_published_module_assets()
    {
        $code = $this->artisan('plugin:publish', ['plugin' => 'Blog']);
        $this->assertFileExists(public_path('plugins/blog/script.js'));
        $this->assertSame(0, $code);
    }
}
