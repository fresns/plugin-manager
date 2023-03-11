<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\PluginManager\Support;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process as SymfonyProcess;

class Process
{
    public static function run(string $cmd, mixed $output = null, ?string $cwd = null, array $env = []): SymfonyProcess
    {
        $cwd = $cwd ?? base_path();

        $process = SymfonyProcess::fromShellCommandline($cmd, $cwd);

        $process->setTimeout(900);

        if ($process->isTty()) {
            $process->setTty(true);
        }

        try {
            if ($output !== false) {
                $output = app(OutputInterface::class);
            }
        } catch (\Throwable $e) {
            $output = $output ?? null;
        }

        $envs =  [
            'PATH' => rtrim(`echo \$PATH`),
        ] + $env;

        if ($output) {
            $process->run(
                function ($type, $line) use ($output) {
                    $output->write($line);
                },
                $envs,
            );
        } else {
            $process->run(null, $envs);
        }

        return $process;
    }
}
