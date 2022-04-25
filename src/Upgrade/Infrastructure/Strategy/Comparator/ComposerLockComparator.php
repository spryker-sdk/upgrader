<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\Comparator;

use Upgrade\Application\Dto\Composer\ComposerLockDiffDto;
use ProcessRunner\Application\Service\ProcessRunnerService;

class ComposerLockComparator
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
     * @var \ProcessRunner\Application\Service\ProcessRunnerService
     */
    protected ProcessRunnerService $processRunner;

    /**
     * @param \ProcessRunner\Application\Service\ProcessRunnerService $processRunner
     */
    public function __construct(ProcessRunnerService $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @return \Upgrade\Application\Dto\Composer\ComposerLockDiffDto
     */
    public function getComposerLockDiff(): ComposerLockDiffDto
    {
        $command = sprintf('%s %s', APPLICATION_ROOT_DIR . static::RUNNER, static::JSON_OUTPUT_FLAG);
        $process = $this->processRunner->runProcess(explode(' ', $command));
        $composerLockDiff = json_decode((string)$process->getOutput(), true) ?? [];

        return new ComposerLockDiffDto($composerLockDiff);
    }
}
