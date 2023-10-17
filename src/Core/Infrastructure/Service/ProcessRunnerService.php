<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Core\Infrastructure\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

class ProcessRunnerService implements ProcessRunnerServiceInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param array<string> $command
     * @param array<string, mixed> $env
     *
     * @return \Symfony\Component\Process\Process<string, string>
     */
    public function run(array $command, array $env = []): Process
    {
        $this->logger->debug(sprintf('Run command: %s', implode(' ', $command)), $env);
        $process = new Process($command, (string)getcwd(), $env);
        $process->setTimeout(static::DEFAULT_PROCESS_TIMEOUT);
        $process->run();

        return $process;
    }

    /**
     * @param string $command
     * @param string|null $cwd
     * @param array<mixed>|null $env
     * @param mixed $input
     * @param float|null $timeout
     *
     * @return \Symfony\Component\Process\Process
     */
    public function runFromCommandLine(
        string $command,
        ?string $cwd = null,
        ?array $env = null,
        $input = null,
        ?float $timeout = self::DEFAULT_PROCESS_TIMEOUT
    ): Process {
        $this->logger->debug(sprintf('Run command: %s', $command), ['cwd' => $cwd, 'env' => $env]);
        $process = Process::fromShellCommandline($command, $cwd, $env, $input, $timeout);
        $process->setTimeout(static::DEFAULT_PROCESS_TIMEOUT);
        $process->run();

        return $process;
    }

    /**
     * @param string $command
     * @param string|null $cwd
     * @param array<mixed>|null $env
     * @param mixed $input
     * @param float|null $timeout
     *
     * @return \Symfony\Component\Process\Process
     */
    public function mustRunFromCommandLine(
        string $command,
        ?string $cwd = null,
        ?array $env = null,
        $input = null,
        ?float $timeout = self::DEFAULT_PROCESS_TIMEOUT
    ): Process {
        $this->logger->debug(sprintf('Run command: %s', $command), ['cwd' => $cwd, 'env' => $env]);
        $process = Process::fromShellCommandline($command, $cwd, $env, $input, $timeout);
        $process->mustRun();

        return $process;
    }

    /**
     * @param array<mixed> $command
     * @param string|null $cwd
     * @param array<mixed>|null $env
     * @param mixed $input
     * @param float|null $timeout
     *
     * @return \Symfony\Component\Process\Process
     */
    public function mustRun(
        array $command,
        ?string $cwd = null,
        ?array $env = null,
        $input = null,
        ?float $timeout = self::DEFAULT_PROCESS_TIMEOUT
    ): Process {
        $this->logger->debug(sprintf('Run command: %s', implode(' ', $command)), ['cwd' => $cwd, 'env' => $env]);
        $process = new Process($command, $cwd, $env, $input, $timeout);
        $process->mustRun();

        return $process;
    }
}
