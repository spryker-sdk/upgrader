<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ProcessRunner\Application\Service;

use ProcessRunner\Infrastructure\Process\ProcessRunner;
use Symfony\Component\Process\Process;

class ProcessRunnerService implements ProcessRunnerServiceInterface
{
    /**
     * @var ProcessRunner
     */
    protected ProcessRunner $processRunner;

    /**
     * @param ProcessRunner $processRunner
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
    public function runProcess(array $command): Process
    {
        return $this->processRunner->runProcess($command);
    }
}
