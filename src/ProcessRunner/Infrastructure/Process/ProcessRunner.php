<?php

namespace ProcessRunner\Infrastructure\Process;

use ProcessRunner\Application\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Process\Process;

class ProcessRunner implements ProcessRunnerServiceInterface
{
    /**
     * @param array $command
     *
     * @return \Symfony\Component\Process\Process
     */
    public function runProcess(array $command): Process
    {
        var_dump(implode(' ', $command));

        $process = new Process($command, (string)getcwd());
        $process->setTimeout(300);
        $process->run();

        return $process;
    }
}
