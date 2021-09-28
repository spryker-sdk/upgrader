<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem\Transfer\Collection;

use Upgrader\Business\Collection\UpgraderCollection;
use Upgrader\Business\PackageManagementSystem\Transfer\ReleaseGroupTransfer;

/**
 * @method \Upgrader\Business\PackageManagementSystem\Transfer\ReleaseGroupTransfer[]|\ArrayIterator|\Traversable getIterator()
 */
class ReleaseGroupTransferCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return ReleaseGroupTransfer::class;
    }
}
