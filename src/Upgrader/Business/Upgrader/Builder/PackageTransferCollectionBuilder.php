<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Builder;

use Upgrader\Business\PackageManagementSystem\Transfer\Collection\ModuleTransferCollection;
use Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection;
use Upgrader\Business\PackageManager\Transfer\PackageTransfer;
use Upgrader\Business\Upgrader\Bridge\PackageCollectionBuilderInterface;
use Upgrader\Business\Upgrader\Validator\PackageSoftValidatorInterface;

class PackageTransferCollectionBuilder implements PackageCollectionBuilderInterface
{
    /**
     * @var \Upgrader\Business\Upgrader\Validator\PackageSoftValidatorInterface
     */
    protected $packageValidateManager;

    /**
     * @param \Upgrader\Business\Upgrader\Validator\PackageSoftValidatorInterface $packageValidateManager
     */
    public function __construct(PackageSoftValidatorInterface $packageValidateManager)
    {
        $this->packageValidateManager = $packageValidateManager;
    }

    /**
     * @param \Upgrader\Business\PackageManagementSystem\Transfer\Collection\ModuleTransferCollection $moduleCollection
     *
     * @return \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection
     */
    public function createFromModuleCollection(ModuleTransferCollection $moduleCollection): PackageTransferCollection
    {
        $packageCollection = new PackageTransferCollection();

        foreach ($moduleCollection as $module) {
            $name = $this->removeTypeFromPackageName($module->getName());
            $package = new PackageTransfer($name, $module->getVersion());
            $packageCollection->add($package);
        }

        return $packageCollection;
    }

    /**
     * @param \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection $packageCollection
     *
     * @return \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection
     */
    public function filterInvalidPackage(PackageTransferCollection $packageCollection): PackageTransferCollection
    {
        $resultCollection = new PackageTransferCollection();

        foreach ($packageCollection as $package) {
            $validateResult = $this->packageValidateManager->isValidPackage($package);
            if ($validateResult->isSuccess()) {
                $resultCollection->add($package);
            }
        }

        return $resultCollection;
    }

    /**
     * @param string $moduleName
     *
     * @return string
     *
     * spryker-shop/shop.shop-ui -> spryker-shop/shop-ui
     */
    protected function removeTypeFromPackageName(string $moduleName): string
    {
        return (string)preg_replace('/\/[a-z]*\./m', '/', $moduleName);
    }
}
