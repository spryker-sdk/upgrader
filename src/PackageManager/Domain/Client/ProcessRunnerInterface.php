<?php

namespace PackageManager\Domain\Client;

use PackageManager\Domain\Dto\PackageManagerResponseDto;

interface ProcessRunnerInterface
{

    /**
     * @param array $command
     *
     * @return \Symfony\Component\Process\Process
     */
    public function runProcess(array $command): PackageManagerResponseDto;

}
