<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManager\Client\ComposerLockDiff;

use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto;

interface ComposerLockDiffCallExecutorInterface
{
    /**
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto
     */
    public function getComposerLockDiff(): PackageManagerResponseDtoDto;
}
