<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Entity\Collection;

use Upgrader\Business\Collection\UpgraderCollection;
use Upgrader\Business\PackageManager\Entity\Package;

/**
 * @method \Upgrader\Business\PackageManager\Entity\Package[]|\ArrayIterator|\Traversable getIterator()
 */
class PackageCollection extends UpgraderCollection implements PackageCollectionInterface
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return Package::class;
    }
}
