<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
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
