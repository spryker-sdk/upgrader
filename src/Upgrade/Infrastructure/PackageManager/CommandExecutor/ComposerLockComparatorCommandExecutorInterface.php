<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManager\CommandExecutor;

use Upgrade\Application\Dto\ComposerLockDiffDto;

interface ComposerLockComparatorCommandExecutorInterface
{
    /**
     * @return \Upgrade\Application\Dto\ComposerLockDiffDto
     */
    public function getComposerLockDiff(): ComposerLockDiffDto;
}
