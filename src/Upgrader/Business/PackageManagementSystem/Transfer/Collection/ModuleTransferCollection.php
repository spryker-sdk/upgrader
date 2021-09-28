<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem\Transfer\Collection;

use Upgrader\Business\Collection\UpgraderCollection;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\ReleaseAppConst;
use Upgrader\Business\PackageManagementSystem\Transfer\ModuletTransfer;

/**
 * @method \Upgrader\Business\PackageManagementSystem\Transfer\ModuletTransfer[]|\ArrayIterator|\Traversable getIterator()
 */
class ModuleTransferCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return ModuletTransfer::class;
    }

    /**
     * @return bool
     */
    public function isContainsMajorUpdates(): bool
    {
        foreach ($this as $module) {
            if ($module->getVersionType() == ReleaseAppConst::MODULE_TYPE_MAJOR) {
                return true;
            }
        }

        return false;
    }
}
