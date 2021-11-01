<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Client\ComposerLockDiff;

use Symfony\Component\Process\Process;
use Upgrader\Business\PackageManager\CallExecutor\CallExecutor;
use Upgrader\Business\PackageManager\Response\PackageManagerResponse;

class ComposerLockDiffCallExecutor implements ComposerLockDiffCallExecutorInterface
{
    /**
     * @var string
     */
    protected const COMMAND_NAME = 'composer-lock-diff';

    /**
     * @var string
     */
    protected const JSON_OUTPUT_FLAG = '--json';

    /**
     * @var \Upgrader\Business\PackageManager\CallExecutor\CallExecutor
     */
    protected $callExecutor;

    /**
     * @param \Upgrader\Business\PackageManager\CallExecutor\CallExecutor $callExecutor
     */
    public function __construct(CallExecutor $callExecutor)
    {
        $this->callExecutor = $callExecutor;
    }

    /**
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    public function getComposerLockDiff(): PackageManagerResponse
    {
        $command = sprintf('%s %s', static::COMMAND_NAME, static::JSON_OUTPUT_FLAG);
        $process = $this->callExecutor->runProcess($command);

        return $this->createResponse($process);
    }

    /**
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    protected function createResponse(Process $process): PackageManagerResponse
    {
        return new PackageManagerResponse($process->isSuccessful(), $process->getOutput());
    }
}
