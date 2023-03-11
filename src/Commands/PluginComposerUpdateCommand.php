<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Fresns\PluginManager\Support\Process;
use Illuminate\Console\Command;

class PluginComposerUpdateCommand extends Command
{
    protected $signature = 'plugin:composer-update';

    protected $description = 'Update all plugins composer package';

    public function handle()
    {
        $process = Process::run(<<<"SHELL"
            echo "current user:" `whoami`
            echo "home path permission is:" `ls -ld ~`
            echo ""

            #test -f ~/.config/composer/compsoer.json && echo 1 || (mkdir -p ~/.config/composer && echo "{}" > ~/.config/composer/composer.json)
            #echo ""

            echo "global composer.json content": `cat ~/.config/composer/composer.json`
            echo ""

            echo "PATH:" `echo \$PATH`
            echo ""
           
            echo "php:" `which php` "\n version" `php -v`
            echo "composer:" `which composer` "\n version" `composer --version`
            echo "git:" `which git` "\n version" `git --version`
            echo ""

            # install command
            composer diagnose
            composer update
        SHELL, $this->output);

        if (! $process->isSuccessful()) {
            $this->error('Failed to install packages, calc composer.json hash value fail');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
