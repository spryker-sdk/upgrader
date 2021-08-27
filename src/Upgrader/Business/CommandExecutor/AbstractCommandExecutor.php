<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\CommandExecutor;

use Exception;
use Symfony\Component\Process\Process;

abstract class AbstractCommandExecutor implements CommandExecutorInterface
{
    private const DEFAULT_COMMAND_TIMEOUT = 60;
    private const ERROR_DESCRIPTION = 'Command: [%s]' . PHP_EOL . 'Error: %s';

    /**
     * @var \Upgrader\Business\CommandExecutor\CommandResultBuilder
     */
    private $resultBuilder;

    public function __construct()
    {
        $this->resultBuilder = new CommandResultBuilder();
    }

    /**
     * @return string
     */
    abstract public function getCommand(): string;

    /**
     * @return int
     */
    public function getRequestTimeout(): int
    {
        return self::DEFAULT_COMMAND_TIMEOUT;
    }

    /**
     * @param string|null $command
     *
     * @throws \Exception
     *
     * @return \Upgrader\Business\CommandExecutor\CommandResultDto
     */
    public function execSuccess(?string $command = null): CommandResultDto
    {
        $process = $this->run($command);

        if ($process->getExitCode()) {
            $errorDescription = sprintf(self::ERROR_DESCRIPTION, $process->getCommandLine(), $process->getErrorOutput());

            throw new Exception($errorDescription);
        }

        return $this->resultBuilder->createResult($process);
    }

    /**
     * @param string|null $command
     *
     * @return \Upgrader\Business\CommandExecutor\CommandResultDto
     */
    public function exec(?string $command = null): CommandResultDto
    {
        $process = $this->run($command);

        return $this->resultBuilder->createResult($process);
    }

    /**
     * @param string|null $command
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function run(?string $command = null): Process
    {
        $process = new Process($this->getCommandAsArray($command), (string)getcwd());
        $process->setTimeout($this->getRequestTimeout());
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
}
