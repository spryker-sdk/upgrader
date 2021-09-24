<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Manager;

use Upgrader\Business\DataProvider\Entity\Collection\ModuleCollection;
use Upgrader\Business\PackageManager\Entity\Collection\PackageCollection;
use Upgrader\Business\PackageManager\Entity\Package;
use Upgrader\Business\Upgrader\Validator\PackageValidateManagerInterface;

class PackageCollectionManager implements PackageCollectionManagerInterface
{
    /**
     * @var \Upgrader\Business\Upgrader\Validator\PackageValidateManagerInterface
     */
    protected $packageValidateManager;

    /**
     * @param \Upgrader\Business\Upgrader\Validator\PackageValidateManagerInterface $packageValidateManager
     */
    public function __construct(PackageValidateManagerInterface $packageValidateManager)
    {
        $this->packageValidateManager = $packageValidateManager;
    }

    /**
     * @param \Upgrader\Business\DataProvider\Entity\Collection\ModuleCollection $moduleCollection
     *
     * @return \Upgrader\Business\PackageManager\Entity\Collection\PackageCollection
     */
    public function createCollectionFromModuleCollection(ModuleCollection $moduleCollection): PackageCollection
    {
        $packageCollection = new PackageCollection();

        foreach ($moduleCollection as $module) {
            $package = new Package($module->getName(), $module->getVersion());
            $packageCollection->add($package);
        }

        return $packageCollection;
    }

    /**
     * @param \Upgrader\Business\PackageManager\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrader\Business\PackageManager\Entity\Collection\PackageCollection
     */
    public function filterInvalidPackage(PackageCollection $packageCollection): PackageCollection
    {
        $resultCollection = new PackageCollection();

        foreach ($packageCollection as $package) {
            $validateResult = $this->packageValidateManager->isValidPackage($package);
            if ($validateResult->isSuccess()) {
                $resultCollection->add($package);
            }
        }

        return $resultCollection;
    }
}
