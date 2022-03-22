<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Tests\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Event;
use Fresns\PluginManager\Models\Plugin;
use Fresns\PluginManager\Tests\TestCase;
use Fresns\PluginManager\Contracts\RepositoryInterface;

class PluginInstallCommandTest extends TestCase
{
    private Filesystem $filesystem;

    private string $localPath;

    private RepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->localPath = __DIR__.'/../stubs/valid/';
        $this->filesystem = $this->app['files'];
        $this->repository = $this->app['plugins.repository'];
    }

    public function tearDown(): void
    {
        $this->repository->find('PluginOne')?->delete();
        $this->repository->find('Plugin3')?->delete();

        parent::tearDown();
    }

    public function test_it_can_local_install_by_directory()
    {

        $this->artisan('plugin:install', ['path' => $this->localPath.'PluginOne', 'plugin' => 'PluginOne']);
        
        $this->assertDirectoryExists($this->repository->find('PluginOne')?->getPath());
        $this->assertTrue($this->repository->find('PluginOne')?->isEnabled());
    }

    public function test_it_can_local_install_by_zip()
    {

        $this->artisan('plugin:install', ['path' => $this->localPath.'Plugin3.zip', 'plugin' => 'Plugin3']);

        $this->assertDirectoryExists($this->repository->find('Plugin3')?->getPath());
        $this->assertTrue($this->repository->find('Plugin3')?->isEnabled());
    }
}
