<?php

namespace Fresns\PluginManager\Tests\Commands;

use Illuminate\Filesystem\Filesystem;
use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\Tests\TestCase;
use Illuminate\Support\Str;

class PluginMakeJobCommandTest extends TestCase
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

    public function test_it_generates_a_queued_job_file()
    {
        $code = $this->artisan('plugin:make-job', ['name' => 'FooBarJob', 'plugin' => 'Blog']);

        $this->assertFileExists($filepath = $this->pluginPath.'/Jobs/FooBarJob.php');


        $this->assertTrue(Str::contains($content = file_get_contents($filepath), 'namespace Plugins\Blog\Jobs;'));
        $this->assertTrue(Str::contains($content, 'class FooBarJob'));
        $this->assertTrue(Str::contains($content, 'use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;'));
        $this->assertSame(0, $code);
    }

    public function test_it_generates_a_job_file()
    {
        $code = $this->artisan('plugin:make-job', [
            'name' => 'FooBarJob',
            'plugin' => 'Blog',
            '--sync' => true,
        ]);

        $this->assertFileExists($filepath = $this->pluginPath.'/Jobs/FooBarJob.php');


        $this->assertTrue(Str::contains($content = file_get_contents($filepath), 'namespace Plugins\Blog\Jobs;'));
        $this->assertTrue(Str::contains($content, 'class FooBarJob'));
        $this->assertFalse(Str::contains($content, 'use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;'));
        $this->assertSame(0, $code);
    }
}
