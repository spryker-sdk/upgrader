<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PackageManager\Domain\ComposerLockComparator;

use PackageManager\Domain\ProcessRunner\ProcessRunnerInterface;
use PackageManager\Domain\Dto\ComposerLockDiffDto;

class ComposerLockComparatorCommandExecutor implements ComposerLockComparatorCommandExecutorInterface
{
    /**
     * @var string
     */
    protected const RUNNER = '/vendor/bin/composer-lock-diff';

    /**
     * @var string
     */
    protected const JSON_OUTPUT_FLAG = '--json';

    /**
     * @var ProcessRunnerInterface
     */
    protected ProcessRunnerInterface $processRunner;

    /**
     * @param ProcessRunnerInterface $processRunner
     */
    public function __construct(ProcessRunnerInterface $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @return \PackageManager\Domain\Dto\ComposerLockDiffDto
     */
    public function getComposerLockDiff(): ComposerLockDiffDto
    {
        $command = sprintf('%s %s', APPLICATION_ROOT_DIR . static::RUNNER, static::JSON_OUTPUT_FLAG);
        $process = $this->processRunner->runCommand(explode(' ', $command));
        $composerLockDiff = json_decode((string)$process->getOutput(), true) ?? [];

        return new ComposerLockDiffDto($composerLockDiff);
    }
}
