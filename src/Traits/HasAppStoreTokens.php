<?php

namespace Fresns\PluginManager\Traits;

use Exception;
use Fresns\PluginManager\Support\Config;

trait HasAppStoreTokens
{
    /**
     * @throws Exception
     */
    public function ensure_api_token_is_available(): void
    {
        if (!Config::get('token')) {
            throw new Exception("Please authenticate using the 'login' command before proceeding.");
        }
    }
}
