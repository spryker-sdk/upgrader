<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PackageManager\Domain\Client\ComposerLockDiff;

use PackageManager\Domain\Dto\PackageManagerResponseDto;

interface ComposerLockDiffCommandExecutorInterface
{
    /**
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function getComposerLockDiff(): PackageManagerResponseDto;
}
