<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Tests\Commands;

use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Event;

class InstallCommandTest extends TestCase
{
    private Filesystem $filesystem;

    private string $localPath;

    private RepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->localPath = __DIR__.'/../stubs/valid/';
        $this->filesystem = $this->app['files'];
        $this->repository = $this->app[RepositoryInterface::class];
    }

    public function tearDown(): void
    {
        optional($this->repository->find('PluginOne'))->delete();
        optional($this->repository->find('Plugin3'))->delete();
        parent::tearDown();
    }

    public function test_it_can_local_install_by_directory()
    {
        Event::fake();
        $this->artisan('plugin:install', ['path' => $this->localPath.'PluginOne']);
        $this->assertDirectoryExists($this->repository->find('PluginOne')->getPath());
        $this->assertTrue($this->repository->find('PluginOne')->isEnabled());
        Event::assertDispatched('plugins.installed');
    }

    public function test_it_can_local_install_by_zip()
    {
        Event::fake();
        $this->artisan('plugin:install', ['path' => $this->localPath.'Plugin3.zip']);
        $this->assertDirectoryExists($this->repository->find('Plugin3')->getPath());
        $this->assertTrue($this->repository->find('Plugin3')->isEnabled());
        Event::assertDispatched('plugins.installed');
    }
}
