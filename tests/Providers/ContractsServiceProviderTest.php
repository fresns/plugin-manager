<?php

namespace Fresns\PluginManager\Tests\Providers;

use Fresns\PluginManager\Contracts\RepositoryInterface;
use Fresns\PluginManager\Support\Repositories\FileRepository;
use Fresns\PluginManager\Tests\TestCase;

class ContractsServiceProviderTest extends TestCase
{
    public function test_it_binds_repository_interface_with_implementation()
    {
        $this->assertInstanceOf(FileRepository::class, app(RepositoryInterface::class));
    }
}
