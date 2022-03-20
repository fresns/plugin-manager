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

class PluginMakeDTOCommandTest extends TestCase
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

    public function test_it_generates_a_plugin_dto_file()
    {
        $code = $this->artisan('plugin:make-dto', ['dto' => 'BlogDTO', 'plugin' => 'Blog']);

        $this->assertFileExists($this->pluginPath.'/DTO/BlogDTO.php');

        $this->assertSame(0, $code);
    }
}
