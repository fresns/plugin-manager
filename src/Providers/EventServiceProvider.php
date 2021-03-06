<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $listens = config('plugins.events');

        if (is_array($listens)) {
            foreach ($listens as $event => $listen) {
                if (is_array($listen)) {
                    foreach ($listen as $value) {
                        Event::listen($event, $value);
                    }
                } else {
                    Event::listen($event, $listen);
                }
            }
        }
    }
}
