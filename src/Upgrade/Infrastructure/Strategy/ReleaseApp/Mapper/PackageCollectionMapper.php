<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Mapper;

use ReleaseAppClient\Domain\Dto\Collection\ModuleDtoCollection;
use PackageManager\Domain\Dto\Collection\PackageDtoCollection;
use PackageManager\Domain\Dto\PackageDto;
use PackageManager\Application\Service\PackageManagerInterface;
use Upgrade\Infrastructure\Strategy\ReleaseApp\Mapper\PackageCollectionMapperInterface;
use Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\PackageSoftValidatorInterface;

class PackageCollectionMapper implements PackageCollectionMapperInterface
{
    /**
     * @var \Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\PackageSoftValidatorInterface
     */
    protected $packageValidator;

    /**
     * @var \PackageManager\Application\Service\PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @param \Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\PackageSoftValidatorInterface $packageValidator
     * @param \PackageManager\Application\Service\PackageManagerInterface $packageManager
     */
    public function __construct(PackageSoftValidatorInterface $packageValidator, PackageManagerInterface $packageManager)
    {
        $this->packageValidator = $packageValidator;
        $this->packageManager = $packageManager;
    }

    /**
     * @param \ReleaseAppClient\Domain\Dto\Collection\ModuleDtoCollection $moduleCollection
     *
     * @return \PackageManager\Domain\Dto\Collection\PackageDtoCollection
     */
    public function mapModuleCollectionToPackageCollection(ModuleDtoCollection $moduleCollection): PackageDtoCollection
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
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection $packageCollection
     *
     * @return \PackageManager\Domain\Dto\Collection\PackageDtoCollection
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
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection $packageCollection
     *
     * @return \PackageManager\Domain\Dto\Collection\PackageDtoCollection
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
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection $packageCollection
     *
     * @return \PackageManager\Domain\Dto\Collection\PackageDtoCollection
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
