<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PackageManager\Infrastructure\Adapter;

use PackageManager\Domain\ProcessRunner\ProcessRunnerInterface;
use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Process\Process;

class ProcessRunnerAdapter implements ProcessRunnerInterface
{
    /**
     * @var \Core\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected $processRunnerService;

    /**
     * @param \Core\Infrastructure\Service\ProcessRunnerServiceInterface $processRunnerService
     */
    public function __construct(ProcessRunnerServiceInterface $processRunnerService)
    {
        $this->processRunnerService = $processRunnerService;
    }

    /**
     * @param array<string> $command
     *
     * @return \Symfony\Component\Process\Process
     */
    public function run(array $command): Process
    {
        return $this->processRunnerService->runCommand($command);
    }
}
