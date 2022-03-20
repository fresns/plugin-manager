<?php

namespace Fresns\PluginManager\Exceptions;

class InvalidActivatorClass extends \Exception
{
    public static function missingConfig(): InvalidActivatorClass
    {
        return new static("You don't have a valid activator configuration class. This might be due to your config being out of date. \n Run php artisan vendor:publish --provider=\"Fresns\PluginManager\Providers\PluginServiceProvider\" --force to publish the up to date configuration");
    }
}
