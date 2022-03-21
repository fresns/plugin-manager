<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Console\Commands;

use Fresns\PluginManager\Traits\HasMarketTokens;
use Fresns\PluginManager\Traits\PluginCommandTrait;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class DeveloperUploadThemeCommand extends Command
{
    use PluginCommandTrait;
    use HasMarketTokens;

    protected $name = 'developer:upload-theme';

    protected $description = 'Package the theme and upload it to the fresns app store';

    protected function getArguments(): array
    {
        return [
            ['theme', InputArgument::REQUIRED, 'The name of theme will be used.'],
        ];
    }
}
