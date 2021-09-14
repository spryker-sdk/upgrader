<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Command;

use Symfony\Component\Process\Process;

class CommandResponse
{
    public const CODE_ERROR = 1;
    public const CODE_SUCCESS = 0;

    /**
     * @var int
     */
    protected $exitCode;

    /**
     * @var string
     */
    protected $processCommand;

    /**
     * @var string
     */
    protected $upgraderCommand;

    /**
     * @var string
     */
    protected $output;

    /**
     * @var string
     */
    protected $errorOutput;

    /**
     * @param \Symfony\Component\Process\Process $process
     * @param string $upgraderCommand
     */
    public function __construct(Process $process, string $upgraderCommand)
    {
        $this->exitCode = $process->isSuccessful() ? self::CODE_SUCCESS : self::CODE_ERROR;
        $this->processCommand = $process->getCommandLine();
        $this->upgraderCommand = $upgraderCommand;
        $this->output = $process->getOutput();
        $this->errorOutput = $process->getErrorOutput();
    }

    /**
     * @return int
     */
    public function getExitCode(): int
    {
        return $this->exitCode;
    }

    /**
     * @return string
     */
    public function getProcessCommand(): string
    {
        return $this->processCommand;
    }

    /**
     * @return string
     */
    public function getUpgraderCommand(): string
    {
        return $this->upgraderCommand;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }

    /**
     * @return string
     */
    public function getErrorOutput(): string
    {
        return $this->errorOutput;
    }
}
