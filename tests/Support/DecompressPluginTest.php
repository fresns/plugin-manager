<?php

namespace Fresns\PluginManager\Tests\Support;

use Fresns\PluginManager\Exceptions\DecompressPluginException;
use Fresns\PluginManager\Support\CompressPlugin;
use Fresns\PluginManager\Support\DecompressPlugin;
use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Tests\TestCase;

class DecompressPluginTest extends TestCase
{
    private Plugin $plugin;
    private string $compressPath = '';

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('plugin:make', ['name' => ['Blog']]);
        $this->plugin = $this->app['plugins.repository']->find('Blog');
    }

    public function tearDown(): void
    {
        $this->app['files']->delete([
            $this->compressPath,
        ]);
        $this->plugin->delete();
        parent::tearDown();
    }

    public function test_it_can_decompress_succeed()
    {
        (new CompressPlugin($this->plugin))->handle();

        $this->compressPath = $compressPath = base_path('plugins/').basename($this->plugin->getCompressFilePath());

        $this->plugin->getFiles()->move($this->plugin->getCompressFilePath(), $compressPath);

        $pluginPath = $this->plugin->getPath();

        $this->plugin->delete();

        $this->assertDirectoryNotExists($pluginPath);

        (new DecompressPlugin($compressPath))->handle();

        $this->assertDirectoryExists($pluginPath);
        $this->assertDirectoryNotExists($compressPath);
    }

    public function test_it_can_decompress_failed()
    {
        $this->expectException(DecompressPluginException::class);
        $this->plugin->getFiles()->copy(__DIR__.'/../stubs/valid/Test.zip', $this->compressPath = $compressPath = base_path('plugins/Test.zip'));
        (new DecompressPlugin($compressPath))->handle();
    }
}
