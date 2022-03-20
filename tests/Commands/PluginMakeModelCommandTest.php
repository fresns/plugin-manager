<?php

namespace Fresns\PluginManager\Tests\Commands;

use Illuminate\Filesystem\Filesystem;
use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\Tests\TestCase;

class PluginMakeModelCommandTest extends TestCase
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

    public function test_it_generates_a_model_file()
    {
        $code = $this->artisan('plugin:make-model', ['model' => 'Post', 'plugin' => 'Blog']);
        $this->assertFileExists($this->pluginPath.'/Models/Post.php');

        $fileCount = count($this->finder->files($this->pluginPath."/Models/"));
        $this->assertGreaterThan(0, $fileCount, 'Create model file failure.');

        $this->assertSame(0, $code);
    }

    public function test_it_generates_a_model_file_and_a_migration_file()
    {
        $code = $this->artisan('plugin:make-model', ['model' => 'Post', 'plugin' => 'Blog', '-m' => true]);
        $this->assertFileExists($this->pluginPath.'/Models/Post.php');

        $fileCount = count($this->finder->files($this->pluginPath."/Database/Migrations/"));
        $this->assertGreaterThan(0, $fileCount, 'Create model migration file failure.');

        $this->assertSame(0, $code);
    }
}
