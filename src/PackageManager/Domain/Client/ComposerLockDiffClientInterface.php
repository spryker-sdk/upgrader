<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PackageManager\Domain\Client;

use PackageManager\Domain\Dto\Collection\PackageManagerResponseDtoCollection;

interface ComposerLockDiffClientInterface
{
    /**
     * @return \PackageManager\Domain\Dto\Collection\PackageManagerResponseDtoCollection
     */
    public function getComposerLockDiff(): PackageManagerResponseDtoCollection;
}
