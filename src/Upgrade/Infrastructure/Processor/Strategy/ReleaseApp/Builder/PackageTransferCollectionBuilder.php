<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Builder;

use Upgrade\Application\Dto\PackageManagementSystem\Collection\ModuleDtoCollection;
use Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection;
use Upgrade\Application\Dto\PackageManager\PackageDto;
use Upgrade\Infrastructure\PackageManager\PackageManagerInterface;
use Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Bridge\PackageCollectionBuilderInterface;
use Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Validator\PackageSoftValidatorInterface;

class PackageTransferCollectionBuilder implements PackageCollectionBuilderInterface
{
    /**
     * @var \Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Validator\PackageSoftValidatorInterface
     */
    protected $packageValidator;

    /**
     * @var \Upgrade\Infrastructure\PackageManager\PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @param \Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Validator\PackageSoftValidatorInterface $packageValidator
     * @param \Upgrade\Infrastructure\PackageManager\PackageManagerInterface $packageManager
     */
    public function __construct(PackageSoftValidatorInterface $packageValidator, PackageManagerInterface $packageManager)
    {
        $this->packageValidator = $packageValidator;
        $this->packageManager = $packageManager;
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManagementSystem\Collection\ModuleDtoCollection $moduleCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection
     */
    public function createFromModuleCollection(ModuleDtoCollection $moduleCollection): PackageDtoCollection
    {
        $packageCollection = new PackageDtoCollection();

        foreach ($moduleCollection as $module) {
            $name = $this->removeTypeFromPackageName($module->getName());
            $package = new PackageDto($name, $module->getVersion());
            $packageCollection->add($package);
        }

        return $packageCollection;
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection
     */
    public function filterInvalidPackage(PackageDtoCollection $packageCollection): PackageDtoCollection
    {
        $resultCollection = new PackageDtoCollection();

        foreach ($packageCollection as $package) {
            $validateResult = $this->packageValidator->isValidPackage($package);
            if ($validateResult->isSuccess()) {
                $resultCollection->add($package);
            }
        }

        return $resultCollection;
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection
     */
    public function getRequiredPackages(PackageDtoCollection $packageCollection): PackageDtoCollection
    {
        $resultCollection = new PackageDtoCollection();

        foreach ($packageCollection as $package) {
            if (!$this->packageManager->isDevPackage($package->getName())) {
                $resultCollection->add($package);
            }
        }

        return $resultCollection;
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection
     */
    public function getRequiredDevPackages(PackageDtoCollection $packageCollection): PackageDtoCollection
    {
        $resultCollection = new PackageDtoCollection();

        foreach ($packageCollection as $package) {
            if ($this->packageManager->isDevPackage($package->getName())) {
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
