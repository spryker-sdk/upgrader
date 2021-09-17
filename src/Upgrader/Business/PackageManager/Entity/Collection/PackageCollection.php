<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
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
