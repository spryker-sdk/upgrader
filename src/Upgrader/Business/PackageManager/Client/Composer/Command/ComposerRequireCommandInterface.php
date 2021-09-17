<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\PackageManager\Client\Composer\Command;

use Upgrader\Business\Command\CommandInterface;
use Upgrader\Business\PackageManager\Entity\Collection\PackageCollectionInterface;

interface ComposerRequireCommandInterface extends CommandInterface
{
    /**
     * @param \Upgrader\Business\PackageManager\Entity\Collection\PackageCollectionInterface $packageCollection
     *
     * @return bool
     */
    public function setPackageCollection(PackageCollectionInterface $packageCollection): bool;
}
