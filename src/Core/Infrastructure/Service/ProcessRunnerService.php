<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Core\Infrastructure\Service;

use Core\Infrastructure\ProcessRunner\ProcessRunner;
use Symfony\Component\Process\Process;

class ProcessRunnerService implements ProcessRunnerServiceInterface
{
    /**
     * @var \Core\Infrastructure\ProcessRunner\ProcessRunner
     */
    protected ProcessRunner $processRunner;

    /**
     * @param \Core\Infrastructure\ProcessRunner\ProcessRunner $processRunner
     */
    public function __construct(ProcessRunner $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @param array $command
     *
     * @return \Symfony\Component\Process\Process
     */
    public function runCommand(array $command): Process
    {
        return $this->processRunner->runCommand($command);
    }
}
