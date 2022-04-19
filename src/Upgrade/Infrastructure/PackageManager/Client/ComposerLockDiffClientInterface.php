<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManager\Client;

use Upgrade\Application\Dto\PackageManager\Collection\PackageManagerResponseDtoCollection;

interface ComposerLockDiffClientInterface
{
    /**
     * @return \Upgrade\Application\Dto\PackageManager\Collection\PackageManagerResponseDtoCollection
     */
    public function getComposerLockDiff(): PackageManagerResponseDtoCollection;
}
