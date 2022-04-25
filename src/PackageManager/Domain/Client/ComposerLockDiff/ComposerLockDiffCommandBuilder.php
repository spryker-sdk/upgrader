<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PackageManager\Domain\Client\ComposerLockDiff;

use Symfony\Component\Process\Process;
use PackageManager\Domain\Dto\PackageManagerResponseDto;
use ProcessRunner\Application\Service\ProcessRunnerServiceInterface;

class ComposerLockDiffCommandBuilder implements ComposerLockDiffCommandBuilderInterface
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
     * @var \ProcessRunner\Application\Service\ProcessRunnerServiceInterface
     */
    protected $processRunner;

    /**
     * @param \ProcessRunner\Application\Service\ProcessRunnerServiceInterface $processRunner
     */
    public function __construct(ProcessRunnerServiceInterface $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
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
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    protected function createResponse(Process $process): PackageManagerResponseDto
    {
        return new PackageManagerResponseDto($process->isSuccessful(), $process->getOutput());
    }
}
