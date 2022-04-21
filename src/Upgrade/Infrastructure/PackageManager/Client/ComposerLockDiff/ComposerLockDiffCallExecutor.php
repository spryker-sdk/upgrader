<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManager\Client\ComposerLockDiff;

use Symfony\Component\Process\Process;
use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto;
use Upgrade\Infrastructure\Process\ProcessRunnerInterface;

class ComposerLockDiffCallExecutor implements ComposerLockDiffCallExecutorInterface
{
    /**
     * @var string
     */
    protected const RUNNER = APPLICATION_ROOT_DIR . '/vendor' . '/bin/composer-lock-diff';

    /**
     * @var string
     */
    protected const JSON_OUTPUT_FLAG = '--json';

    /**
     * @var \Upgrade\Infrastructure\Process\ProcessRunnerInterface
     */
    protected $processRunner;

    /**
     * @param \Upgrade\Infrastructure\Process\ProcessRunnerInterface $processRunner
     */
    public function __construct(ProcessRunnerInterface $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto
     */
    public function getComposerLockDiff(): PackageManagerResponseDto
    {
        $command = sprintf('%s %s', static::RUNNER, static::JSON_OUTPUT_FLAG);
        $process = $this->processRunner->runProcess(explode(' ', $command));

        return $this->createResponse($process);
    }

    /**
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto
     */
    protected function createResponse(Process $process): PackageManagerResponseDto
    {
        return new PackageManagerResponseDto($process->isSuccessful(), $process->getOutput());
    }
}
