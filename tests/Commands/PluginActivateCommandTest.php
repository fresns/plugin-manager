<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Tests\Commands;

use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class PluginActivateCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('plugin:make', ['name' => ['Blog']]);
        $this->artisan('plugin:make', ['name' => ['Taxonomy']]);
    }

    public function tearDown(): void
    {
        $this->app[RepositoryInterface::class]->delete('Blog');
        $this->app[RepositoryInterface::class]->deletePluginDirectory('Blog');
        $this->app[RepositoryInterface::class]->delete('Taxonomy');
        $this->app[RepositoryInterface::class]->deletePluginDirectory('Taxonomy');
        parent::tearDown();
    }

    public function test_it_enables_a_plugin()
    {
        Event::fake();
        /** @var Plugin $blogPlugin */
        $blogPlugin = $this->app[RepositoryInterface::class]->find('Blog');
        $blogPlugin->deactivate();

        $code = $this->artisan('plugin:activate', ['plugin' => 'Blog']);

        $this->assertTrue($blogPlugin->isEnabled());
        $this->assertSame(0, $code);
        Event::assertDispatched('plugins.activating');
        Event::assertDispatched('plugins.activated');
    }

    public function test_it_enables_all_plugins()
    {
        Event::fake();
        /** @var Plugin $blogPlugin */
        $blogPlugin = $this->app[RepositoryInterface::class]->find('Blog');
        $blogPlugin->deactivate();

        /** @var Plugin $taxonomyPlugin */
        $taxonomyPlugin = $this->app[RepositoryInterface::class]->find('Taxonomy');
        $taxonomyPlugin->deactivate();

        $code = $this->artisan('plugin:activate');

        $this->assertTrue($blogPlugin->isEnabled() && $taxonomyPlugin->isEnabled());
        $this->assertSame(0, $code);
        Event::assertDispatched('plugins.activating');
        Event::assertDispatched('plugins.activated');
    }
}
