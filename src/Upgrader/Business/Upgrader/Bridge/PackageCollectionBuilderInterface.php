<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Bridge;

use Upgrader\Business\PackageManagementSystem\Transfer\Collection\ModuleTransferCollection;
use Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection;

interface PackageCollectionBuilderInterface
{
    /**
     * @param \Upgrader\Business\PackageManagementSystem\Transfer\Collection\ModuleTransferCollection $moduleCollection
     *
     * @return \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection
     */
    public function createFromModuleCollection(ModuleTransferCollection $moduleCollection): PackageTransferCollection;

    /**
     * @param \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection $packageCollection
     *
     * @return \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection
     */
    public function filterInvalidPackage(PackageTransferCollection $packageCollection): PackageTransferCollection;

    /**
     * @param \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection $packageCollection
     *
     * @return \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection
     */
    public function getRequiredPackages(PackageTransferCollection $packageCollection): PackageTransferCollection;

    /**
     * @param \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection $packageCollection
     *
     * @return \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection
     */
    public function getRequiredDevPackages(PackageTransferCollection $packageCollection): PackageTransferCollection;
}
