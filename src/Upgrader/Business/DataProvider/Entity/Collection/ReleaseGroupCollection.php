<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\DataProvider\Entity\Collection;

use Upgrader\Business\Collection\UpgraderCollection;
use Upgrader\Business\DataProvider\Entity\ReleaseGroup;

/**
 * @method \Upgrader\Business\DataProvider\Entity\ReleaseGroup[]|\ArrayIterator|\Traversable getIterator()
 */
class ReleaseGroupCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return ReleaseGroup::class;
    }
}
