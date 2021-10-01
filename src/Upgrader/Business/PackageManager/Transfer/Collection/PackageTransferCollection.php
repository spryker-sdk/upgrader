<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Transfer\Collection;

use Upgrader\Business\Collection\UpgraderCollection;
use Upgrader\Business\PackageManager\Transfer\PackageTransfer;

/**
 * @method \Upgrader\Business\PackageManager\Transfer\PackageTransfer[]|\ArrayIterator|\Traversable getIterator()
 */
class PackageTransferCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return PackageTransfer::class;
    }

    /**
     * @return array
     */
    public function getNameList(): array
    {
        $result = [];

        foreach ($this as $package) {
            $result[] = (string)$package;
        }

        return $result;
    }
}
