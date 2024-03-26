<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Commands;

use Illuminate\Console\Command;

class FresnsCommand extends Command
{
    protected $signature = 'fresns {action?}';

    protected $description = 'Tips export PATH command';

    public function handle()
    {
        $vendorBinPath = base_path('vendor/bin');

        $action = $this->argument('action') ?? 'activate';
        if (method_exists($this, $action)) {
            $this->{$action}($vendorBinPath);
        }

        return Command::SUCCESS;
    }

    public function activate(string $vendorBinPath)
    {
        $rootDir = base_path();
        $command = sprintf('export %s', "PATH=$rootDir:$vendorBinPath:".'$PATH');
        if (! str_contains(getenv('PATH'), $vendorBinPath)) {
            $this->warn('Add Project vendorBinPath');
            $this->line('');
            $this->warn('Please input this command on your terminal:');
            $this->line($command);

            $this->line('');
            $this->warn('Then rerun command to get usage help:');
            $this->line('fresns plugin');
        } else {
            $this->warn('Already Add Project vendorBinPath: ');
            $this->line($vendorBinPath);

            $this->line('');
            $this->info('Now you can run command:');
            $this->line('fresns');
        }
    }

    public function deactivate(string $vendorBinPath)
    {
        $pathExcludeProjectBin = str_replace($vendorBinPath, '', getenv('PATH'));
        $fixErrorPath = str_replace(['::'], '', $pathExcludeProjectBin);
        $command = sprintf('export %s', "PATH=$fixErrorPath");

        $this->warn('Remove Project vendorBinPath');
        $this->line('');
        $this->warn('Please input this command on your terminal:');
        $this->line('');
        $this->line($command);
    }
}
