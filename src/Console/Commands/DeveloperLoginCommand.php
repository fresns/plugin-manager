<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Support\Config;
use Illuminate\Console\Command;

class DeveloperLoginCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'developer:login';

    /**
     * @var string
     */
    protected $description = 'Login to the developer server.';

    public function handle(): int
    {
        try {
            $result = app('plugins.client')->login(
                $email = $this->ask('Email'),
                $password = $this->secret('Password')
            );
            $this->store(data_get($result, 'token'));

            return 0;
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());

            return E_ERROR;
        }
    }

    protected function store($token): void
    {
        Config::set('token', $token);

        $this->info('Authenticated successfully.'.PHP_EOL);
    }
}
