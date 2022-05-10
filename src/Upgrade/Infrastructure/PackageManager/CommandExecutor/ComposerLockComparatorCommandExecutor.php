<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManager\CommandExecutor;

use Core\Infrastructure\Service\ProcessRunnerServiceInterface;
use Upgrade\Application\Dto\ComposerLockDiffDto;

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
     * @var \Core\Infrastructure\Service\ProcessRunnerServiceInterface
     */
    protected ProcessRunnerServiceInterface $processRunner;

    /**
     * @param \Core\Infrastructure\Service\ProcessRunnerServiceInterface $processRunner
     */
    public function __construct(ProcessRunnerServiceInterface $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    /**
     * @return \Upgrade\Application\Dto\ComposerLockDiffDto
     */
    public function getComposerLockDiff(): ComposerLockDiffDto
    {
        $command = sprintf('%s %s', APPLICATION_ROOT_DIR . static::RUNNER, static::JSON_OUTPUT_FLAG);
        $process = $this->processRunner->run(explode(' ', $command));
        $composerLockDiff = json_decode((string)$process->getOutput(), true) ?? [];

        return new ComposerLockDiffDto($composerLockDiff);
    }
}
