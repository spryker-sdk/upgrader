<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Manager;

use Upgrader\Business\DataProvider\Entity\Collection\ModuleCollection;
use Upgrader\Business\PackageManager\Entity\Collection\PackageCollection;

interface PackageCollectionManagerInterface
{
    /**
     * @param \Upgrader\Business\DataProvider\Entity\Collection\ModuleCollection $moduleCollection
     *
     * @return \Upgrader\Business\PackageManager\Entity\Collection\PackageCollection
     */
    public function createCollectionFromModuleCollection(ModuleCollection $moduleCollection): PackageCollection;

    /**
     * @param \Upgrader\Business\PackageManager\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrader\Business\PackageManager\Entity\Collection\PackageCollection
     */
    public function filterInvalidPackage(PackageCollection $packageCollection): PackageCollection;
}
