<?php

namespace PackageManager\Domain\ProcessRunner;

use PackageManager\Domain\Dto\PackageManagerResponseDto;
use Symfony\Component\Process\Process;

interface ProcessRunnerInterface
{

    /**
     * @param array $command
     *
     * @return \Symfony\Component\Process\Process
     */
    public function runCommand(array $command): Process;

}
