<?php

namespace Fresns\PluginManager\Tests\Commands;

use Illuminate\Filesystem\Filesystem;
use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\Tests\TestCase;
use Illuminate\Support\Str;

class PluginMakeListenerCommandTest extends TestCase
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

    public function test_it_generates_a_listener_file()
    {
        $code = $this->artisan('plugin:make-listener', ['name' => 'FooBarListener', 'plugin' => 'Blog']);

        $this->assertFileExists($filepath = $this->pluginPath.'/Listeners/FooBarListener.php');


        $this->assertFalse(Str::contains(file_get_contents($filepath), 'use Plugins\Blog\Events\FooBarEvent;'));
        $this->assertFalse(Str::contains(file_get_contents($filepath), '@param  FooBarEvent  $event'));
        $this->assertFalse(Str::contains(file_get_contents($filepath), 'public function handle(FooBarEvent $event)'));

        $this->assertFalse(Str::contains(file_get_contents($filepath), 'use InteractsWithQueue;'));
        $this->assertSame(0, $code);
    }

    public function test_it_generates_a_queued_listener_file()
    {
        $code = $this->artisan('plugin:make-listener', [
            'name' => 'FooBarListener',
            'plugin' => 'Blog',
            '--queued' => true,
        ]);

        $this->assertFileExists($filepath = $this->pluginPath.'/Listeners/FooBarListener.php');

        $this->assertFalse(Str::contains(file_get_contents($filepath), 'use Plugins\Blog\Events\FooBarEvent;'));
        $this->assertFalse(Str::contains(file_get_contents($filepath), '@param  FooBarEvent  $event'));
        $this->assertFalse(Str::contains(file_get_contents($filepath), 'public function handle(FooBarEvent $event)'));

        $this->assertTrue(Str::contains(file_get_contents($filepath), 'use InteractsWithQueue;'));
        $this->assertSame(0, $code);
    }

    public function test_it_generates_a_event_listener_file()
    {
        $code = $this->artisan('plugin:make-listener', [
            'name' => 'FooBarListener',
            'plugin' => 'Blog',
            '--event' => 'FooBarEvent',
        ]);

        $this->assertFileExists($filepath = $this->pluginPath.'/Listeners/FooBarListener.php');

        $this->assertTrue(Str::contains(file_get_contents($filepath), 'use Plugins\Blog\Events\FooBarEvent;'));
        $this->assertTrue(Str::contains(file_get_contents($filepath), '@param  FooBarEvent  $event'));
        $this->assertTrue(Str::contains(file_get_contents($filepath), 'public function handle(FooBarEvent $event)'));

        $this->assertFalse(Str::contains(file_get_contents($filepath), 'use InteractsWithQueue;'));
        $this->assertSame(0, $code);
    }

    public function test_it_generates_a_event_queued_listener_file()
    {
        $code = $this->artisan('plugin:make-listener', [
            'name' => 'FooBarListener',
            'plugin' => 'Blog',
            '--event' => 'FooBarEvent',
            '--queued' => true,
        ]);

        $this->assertFileExists($filepath = $this->pluginPath.'/Listeners/FooBarListener.php');

        $this->assertTrue(Str::contains(file_get_contents($filepath), 'use Plugins\Blog\Events\FooBarEvent;'));
        $this->assertTrue(Str::contains(file_get_contents($filepath), '@param  FooBarEvent  $event'));
        $this->assertTrue(Str::contains(file_get_contents($filepath), 'public function handle(FooBarEvent $event)'));

        $this->assertTrue(Str::contains(file_get_contents($filepath), 'use InteractsWithQueue;'));
        $this->assertSame(0, $code);
    }
}
