<?php

namespace Fresns\PluginManager\Tests\Commands;

use Illuminate\Filesystem\Filesystem;
use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\Tests\TestCase;
use Illuminate\Support\Str;

class PluginMakeRuleCommandTest extends TestCase
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

    public function test_it_generates_a_rule_file()
    {
        $code = $this->artisan('plugin:make-rule', ['name' => 'FooBarRule', 'plugin' => 'Blog']);

        $this->assertFileExists($filepath = $this->pluginPath.'/Rules/FooBarRule.php');


        $this->assertTrue(Str::contains(file_get_contents($filepath), 'namespace Plugins\Blog\Rules;'));
        $this->assertTrue(Str::contains(file_get_contents($filepath), 'class FooBarRule'));
        $this->assertSame(0, $code);
    }
}
