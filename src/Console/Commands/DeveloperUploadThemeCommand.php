<?php

namespace Fresns\PluginManager\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Fresns\PluginManager\Traits\HasAppStoreTokens;
use Fresns\PluginManager\Traits\PluginCommandTrait;

class DeveloperUploadThemeCommand extends Command
{
    use PluginCommandTrait;
    use HasAppStoreTokens;

    protected $name = 'developer:upload-theme';

    protected $description = 'Package the theme and upload it to the fresns app store';

    protected function getArguments(): array
    {
        return [
            ['theme', InputArgument::REQUIRED, 'The name of theme will be used.'],
        ];
    }
}
