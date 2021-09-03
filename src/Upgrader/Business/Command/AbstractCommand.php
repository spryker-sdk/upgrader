<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Command;

use Symfony\Component\Process\Process;
use Upgrader\Business\Command\ResultOutput\CommandResultOutput;
use Upgrader\UpgraderConfig;

abstract class AbstractCommand implements CommandInterface
{
    /**
     * @var \Upgrader\UpgraderConfig
     */
    protected $config;

    /**
     * @param \Upgrader\UpgraderConfig $config
     */
    public function __construct(UpgraderConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    abstract public function getCommand(): string;

    /**
     * @param string|null $command
     *
     * @return \Upgrader\Business\Command\ResultOutput\CommandResultOutput
     */
    public function run(?string $command = null): CommandResultOutput
    {
        $process = $this->runProcess($command);

        return $this->createCommandResultOutput($process);
    }

    /**
     * @param string|null $command
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function runProcess(?string $command = null): Process
    {
        $process = new Process($this->getCommandAsArray($command), (string)getcwd());
        $process->setTimeout($this->config->getCommandExecutionTimeout());
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
     * @return \Upgrader\Business\Command\ResultOutput\CommandResultOutput
     */
    protected function createCommandResultOutput(Process $process): CommandResultOutput
    {
        $resultOutput = $process->getExitCode() ? $process->getErrorOutput() : $process->getExitCodeText();

        return new CommandResultOutput((int)$process->getExitCode(), (string)$resultOutput);
    }
}
