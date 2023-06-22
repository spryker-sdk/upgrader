<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Mapper;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseAppPackageHelper;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Domain\Entity\Package;

class PackageCollectionMapper implements PackageCollectionMapperInterface
{
    /**
     * @var \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected PackageManagerAdapterInterface $packageManager;

    /**
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManager
     */
    public function __construct(PackageManagerAdapterInterface $packageManager)
    {
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
            $name = ReleaseAppPackageHelper::normalizePackageName($module->getName());
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
    public function getRequiredPackages(PackageCollection $packageCollection): PackageCollection
    {
        $resultCollection = new PackageCollection();

        foreach ($packageCollection->toArray() as $package) {
            if (
                !$this->packageManager->isDevPackage($package->getName()) &&
                !$this->packageManager->isSubPackage($package->getName())
            ) {
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
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Domain\Entity\Collection\PackageCollection
     */
    public function getUpdatedPackages(PackageCollection $packageCollection): PackageCollection
    {
        $resultCollection = new PackageCollection();

        foreach ($packageCollection->toArray() as $package) {
            if ($this->packageManager->isSubPackage($package->getName())) {
                $resultCollection->add($package);
            }
        }

        return $resultCollection;
    }
}
