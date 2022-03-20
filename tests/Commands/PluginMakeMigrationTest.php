<?php

namespace Fresns\PluginManager\Tests\Commands;

use Illuminate\Filesystem\Filesystem;
use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\Tests\TestCase;

class PluginMakeMigrationTest extends TestCase
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

    public function test_it_generates_a_migrate_file()
    {
        $code = $this->artisan('plugin:make-migration', ['name' => 'create_posts_table', 'plugin' => 'Blog']);

        $fileCount = count($this->finder->files($this->pluginPath."/Database/Migrations/"));
        $this->assertGreaterThan(0, $fileCount, 'Create migration file failure.');

        $this->assertSame(0, $code);
    }
}
