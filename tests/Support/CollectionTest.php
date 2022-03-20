<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Tests\Support;

use Fresns\PluginManager\Support\Collection;
use Fresns\PluginManager\Support\Plugin;
use Fresns\PluginManager\Tests\TestCase;

class CollectionTest extends TestCase
{
    public function test_to_array_sets_path_attribute()
    {
        $pluginOnePath = __DIR__.'/../stubs/valid/PluginOne';
        $pluginTwoPath = __DIR__.'/../stubs/valid/PluginTwo';
        $plugins = [
            new Plugin($this->app, 'PluginOne', $pluginOnePath),
            new Plugin($this->app, 'PluginTwo', $pluginTwoPath),
        ];
        $collection = new Collection($plugins);
        $collectionArray = $collection->toArray();

        $this->assertArrayHasKey('path', $collectionArray[0]);
        $this->assertEquals($pluginOnePath, $collectionArray[0]['path']);
        $this->assertArrayHasKey('path', $collectionArray[1]);
        $this->assertEquals($pluginTwoPath, $collectionArray[1]['path']);
    }

    /** @test */
    public function getItemsReturnsTheCollectionItems()
    {
        $pluginOnePath = __DIR__.'/../stubs/valid/PluginOne';
        $pluginTwoPath = __DIR__.'/../stubs/valid/PluginTwo';

        $plugins = [
            new Plugin($this->app, 'PluginOne', $pluginOnePath),
            new Plugin($this->app, 'PluginTwo', $pluginTwoPath),
        ];
        $collection = new Collection($plugins);
        $items = $collection->getItems();

        $this->assertCount(2, $items);
        $this->assertInstanceOf(Plugin::class, $items[0]);
    }
}
