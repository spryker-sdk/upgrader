<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PackageManager\Domain\ComposerLockComparator;

use PackageManager\Domain\Dto\ComposerLockDiffDto;

interface ComposerLockComparatorCommandExecutorInterface
{
    /**
     * @return \PackageManager\Domain\Dto\ComposerLockDiffDto
     */
    public function getComposerLockDiff(): ComposerLockDiffDto;
}
