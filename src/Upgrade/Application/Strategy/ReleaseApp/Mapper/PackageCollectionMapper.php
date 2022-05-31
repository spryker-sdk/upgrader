<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Mapper;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Strategy\ReleaseApp\Validator\PackageSoftValidatorInterface;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Domain\Entity\Package;

class PackageCollectionMapper implements PackageCollectionMapperInterface
{
    /**
     * @var \Upgrade\Application\Strategy\ReleaseApp\Validator\PackageSoftValidatorInterface
     */
    protected PackageSoftValidatorInterface $packageValidator;

    /**
     * @var \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected PackageManagerAdapterInterface $packageManager;

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Validator\PackageSoftValidatorInterface $packageValidator
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManager
     */
    public function __construct(PackageSoftValidatorInterface $packageValidator, PackageManagerAdapterInterface $packageManager)
    {
        $this->packageValidator = $packageValidator;
        $this->packageManager = $packageManager;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection $moduleCollection
     *
     * @return \Upgrade\Domain\Entity\Collection\PackageCollection
     */
    public function mapModuleCollectionToPackageCollection(ModuleDtoCollection $moduleCollection): PackageCollection
    {
        $packageCollection = new PackageCollection();

        foreach ($moduleCollection->toArray() as $module) {
            $name = $this->removeTypeFromPackageName($module->getName());
            $package = new Package($name, $module->getVersion());
            $packageCollection->add($package);
        }

        return $packageCollection;
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Domain\Entity\Collection\PackageCollection
     */
    public function filterInvalidPackage(PackageCollection $packageCollection): PackageCollection
    {
        $resultCollection = new PackageCollection();

        foreach ($packageCollection->toArray() as $package) {
            $validateResult = $this->packageValidator->isValidPackage($package);
            if ($validateResult->isSuccessful()) {
                $resultCollection->add($package);
            }
        }

        return $resultCollection;
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Domain\Entity\Collection\PackageCollection
     */
    public function getRequiredPackages(PackageCollection $packageCollection): PackageCollection
    {
        $resultCollection = new PackageCollection();

        foreach ($packageCollection->toArray() as $package) {
            if (!$this->packageManager->isDevPackage($package->getName())) {
                $resultCollection->add($package);
            }
        }

        return $resultCollection;
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Domain\Entity\Collection\PackageCollection
     */
    public function getRequiredDevPackages(PackageCollection $packageCollection): PackageCollection
    {
        $resultCollection = new PackageCollection();

        foreach ($packageCollection->toArray() as $package) {
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
