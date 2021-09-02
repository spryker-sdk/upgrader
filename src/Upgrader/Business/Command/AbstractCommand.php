<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Command;

use Symfony\Component\Process\Process;
use Upgrader\Business\Exception\UpgraderCommandExecException;

abstract class AbstractCommand implements CommandInterface
{
    /**
     * @var int
     */
    protected $execTimeOut;

    /**
     * @param int $execTimeOut
     */
    public function __construct(int $execTimeOut)
    {
        $this->execTimeOut = $execTimeOut;
    }

    /**
     * @return string
     */
    abstract public function getCommand(): string;

    /**
     * @param string|null $command
     *
     * @throws \Upgrader\Business\Exception\UpgraderCommandExecException
     *
     * @return \Upgrader\Business\Command\CommandResultInterface
     */
    public function exec(?string $command = null): CommandResultInterface
    {
        $process = $this->run($command);

        if ($process->getExitCode()) {
            throw new UpgraderCommandExecException($process->getCommandLine(), $process->getErrorOutput());
        }

        return $this->createResult($process);
    }

    /**
     * @param string|null $command
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function run(?string $command = null): Process
    {
        $process = new Process($this->getCommandAsArray($command), (string)getcwd());
        $process->setTimeout($this->execTimeOut);
        $process->run();

        return $process;
    }

    /**
     * @param string|null $command
     *
     * @return string[]
     */
    protected function getCommandAsArray(?string $command): array
    {
        $command = $command ?? $this->getCommand();

        return explode(' ', $command);
    }

    /**
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \Upgrader\Business\Command\CommandResult
     */
    protected function createResult(Process $process): CommandResult
    {
        $resultOutput = $process->getExitCode() ? $process->getErrorOutput() : $process->getExitCodeText();

        return new CommandResult((int)$process->getExitCode(), (string)$resultOutput);
    }
}
