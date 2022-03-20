<?php

namespace Fresns\PluginManager\Tests\Support\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Fresns\PluginManager\Enums\PluginStatus;
use Fresns\PluginManager\Exceptions\PluginNotFoundException;
use Fresns\PluginManager\Models\InstallPlugin;
use Fresns\PluginManager\Support\Repositories\MysqlRepository;
use Fresns\PluginManager\Tests\TestCase;

class MysqlRepositoryTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @var MysqlRepository
     */
    private MysqlRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = new MysqlRepository($this->app);
        $this->initInstallPlugins();
    }

    private function initInstallPlugins()
    {
        $ip1 = InstallPlugin::query()->newModelInstance();
        $ip1->unikey = 'Test1';
        $ip1->name = 'test1a';
        $ip1->type = 1;
        $ip1->description = 'this is test1';
        $ip1->providers = "PluginTest\Test1\Providers\Test1ServiceProvider";
        $ip1->status = PluginStatus::enable();
        $ip1->composer = [
            'require' => [
                'wildbit/swiftmailer-postmark' => '^3.1',
                'zircote/swagger-php' => '2.*',
            ],
            'require-dev' => [
                'spatie/laravel-enum' => '1.6.0',
            ],
        ];
        $ip1->saveOrFail();

        $ip2 = InstallPlugin::query()->newModelInstance();
        $ip2->unikey = 'Test2';
        $ip2->name = 'test2a';
        $ip2->type = 1;
        $ip2->description = 'this is test2';
        $ip2->providers = "PluginTest\Test2\Providers\Test2ServiceProvider";
        $ip2->status = PluginStatus::disable();
        $ip2->composer = [
            'require' => [
                'twilio/sdk' => '^6.28',
                'tymon/jwt-auth' => '^1.0',
                'wildbit/swiftmailer-postmark' => '^3.1',
            ],
            'require-dev' => [
                'laravel/telescope' => '^2.0',
            ],
        ];
        $ip2->saveOrFail();
    }

    public function test_it_can_query_return_builder()
    {
        $this->assertInstanceOf(Builder::class, $this->repository->query());
    }

    public function test_it_returns_all_enabled_plugins()
    {
        $this->assertCount(1, $this->repository->getByStatus(PluginStatus::enable()));
        $this->assertCount(1, $this->repository->allEnabled());
    }

    public function test_it_counts_all_plugins()
    {
        $this->assertEquals(2, $this->repository->count());
    }

    public function test_it_finds_a_plugin()
    {
        $plugin = $this->repository->find('test1');
        $this->assertInstanceOf(InstallPlugin::class, $plugin);
        $this->assertEquals('Test1', $plugin->name);

        $plugin2 = $this->repository->find('tEsT1');
        $this->assertInstanceOf(InstallPlugin::class, $plugin2);
        $this->assertEquals('Test1', $plugin2->name);

        $plugin3 = $this->repository->find('tEsTs1');
        $this->assertNull($plugin3);
    }

    public function test_it_finds_a_plugin_by_alias()
    {
        $plugin = $this->repository->findByAlias('test1a');
        $this->assertInstanceOf(InstallPlugin::class, $plugin);
        $this->assertEquals('test1a', $plugin->alias);

        $plugin2 = $this->repository->findByAlias('tEsT1a');
        $this->assertInstanceOf(InstallPlugin::class, $plugin2);
        $this->assertEquals('test1a', $plugin2->alias);

        $plugin3 = $this->repository->findByAlias('tEsTs');
        $this->assertNull($plugin3);
    }

    public function test_it_find_or_fail_throws_exception_if_plugin_not_found()
    {
        $this->expectException(PluginNotFoundException::class);
        $this->repository->findOrFail('test3');
    }

    public function test_it_find_fail_a_plugin()
    {
        $plugin = $this->repository->findOrFail('test1');
        $this->assertInstanceOf(InstallPlugin::class, $plugin);
        $this->assertEquals('Test1', $plugin->name);
    }
}
