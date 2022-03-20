<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Tests\Commands;

use Fresns\PluginManager\Contracts\ActivatorInterface;
use Fresns\PluginManager\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Event;

class PluginUninstallCommandTest extends TestCase
{
    /**
     * @var Filesystem
     */
    private Filesystem $finder;
    /**
     * @var ActivatorInterface
     */
    private ActivatorInterface $activator;

    public function setUp(): void
    {
        parent::setUp();
        $this->finder = $this->app['files'];
        $this->activator = $this->app[ActivatorInterface::class];
    }

    public function test_it_can_uninstall_a_plugin_from_disk(): void
    {
        Event::fake();
        $this->artisan('plugin:make', ['name' => ['WrongPlugin']]);
        $this->assertDirectoryExists(base_path('plugins/WrongPlugin'));

        $this->artisan('plugin:activate', ['plugin' => 'WrongPlugin']);

        $code = $this->artisan('plugin:uninstall', ['plugin' => 'WrongPlugin']);
        $this->assertDirectoryNotExists(base_path('plugins/WrongPlugin'));
        $this->assertSame(0, $code);
        Event::assertDispatched('plugins.uninstalling');
        Event::assertDispatched('plugins.uninstalled');
    }
}
