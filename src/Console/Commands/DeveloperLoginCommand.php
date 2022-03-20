<?php

namespace Fresns\PluginManager\Console\Commands;

use Illuminate\Console\Command;
use Fresns\PluginManager\Support\Config;

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
