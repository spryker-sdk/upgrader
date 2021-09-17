<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\PackageManager\Entity\Collection;

interface PackageCollectionInterface
{
    /**
     * @return \Upgrader\Business\PackageManager\Entity\PackageInterface[]
     */
    public function toArray(): array;
}
