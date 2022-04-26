<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PackageManager\Domain\Client;

use PackageManager\Domain\ComposerLockComparator\ComposerLockComparatorCommandExecutorInterface;
use PackageManager\Domain\Dto\ComposerLockDiffDto;

class ComposerLockComparatorClient implements ComposerLockComparatorClientInterface
{
    /**
     * @var \PackageManager\Domain\ComposerLockComparator\ComposerLockComparatorCommandExecutorInterface
     */
    protected $composerLockComparatorCommandExecutor;

    /**
     * @param \PackageManager\Domain\ComposerLockComparator\ComposerLockComparatorCommandExecutorInterface $composerLockDiffCallExecutor
     */
    public function __construct(ComposerLockComparatorCommandExecutorInterface $composerLockDiffCallExecutor)
    {
        $this->composerLockComparatorCommandExecutor = $composerLockDiffCallExecutor;
    }

    /**
     * @return \PackageManager\Domain\Dto\ComposerLockDiffDto
     */
    public function getComposerLockDiff(): ComposerLockDiffDto
    {
        return $this->composerLockComparatorCommandExecutor->getComposerLockDiff();
    }
}
