<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\Comparator;

use Upgrade\Infrastructure\Dto\Composer\ComposerLockDiffDto;
use Upgrade\Infrastructure\Process\ProcessRunner;

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
     * @var \Upgrade\Infrastructure\Process\ProcessRunner
     */
    protected ProcessRunner $processRunner;

    /**
     * @param \Upgrade\Infrastructure\Process\ProcessRunner $processRunner
     */
    public function __construct(ProcessRunner $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @return \Upgrade\Infrastructure\Dto\Composer\ComposerLockDiffDto
     */
    public function getComposerLockDiff(): ComposerLockDiffDto
    {
        $command = sprintf('%s %s', APPLICATION_ROOT_DIR . static::RUNNER, static::JSON_OUTPUT_FLAG);
        $process = $this->processRunner->run(explode(' ', $command));
        $composerLockDiff = json_decode((string)$process->getOutput(), true) ?? [];

        return new ComposerLockDiffDto($composerLockDiff);
    }
}
