<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Command;

use Symfony\Component\Process\Process;
use Upgrader\Business\Command\Response\CommandResponse;
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
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    public function run(): CommandResponse
    {
        $process = $this->runProcess($this->getCommand());

        return $this->createCommandResponse($process);
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
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    protected function createCommandResponse(Process $process): CommandResponse
    {
        $resultOutput = $process->getExitCode() ? $process->getErrorOutput() : $process->getExitCodeText();

        return new CommandResponse($process->isSuccessful(), $this->getName(), $resultOutput);
    }

//    /**
//     *
//     * @return \Upgrader\Business\Command\Response\CommandResponse
//     */
//    protected function runProcess(string $cliCommand): CommandResponse
//    {
//        $process = new Process(explode(' ', $cliCommand), (string)getcwd());
//        $process->setTimeout(9000);
//        $process->run();
//        $output = $process->getOutput();
//
//        if(!$process->isSuccessful()){
//            $output .= "\n" . $process->getErrorOutput();
//        }
//
//        return new CommandResponse($process->isSuccessful(), $this->getName(), $output);
//    }
}
