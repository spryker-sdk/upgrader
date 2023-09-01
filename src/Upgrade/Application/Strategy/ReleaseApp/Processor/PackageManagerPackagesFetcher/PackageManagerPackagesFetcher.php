<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor\PackageManagerPackagesFetcher;

use Composer\Semver\Semver;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\PackageManagerPackagesDto;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Domain\Entity\Package;

class PackageManagerPackagesFetcher implements PackageManagerPackagesFetcherInterface
{
    /**
     * @var \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected PackageManagerAdapterInterface $packageManager;

    /**
     * @var bool
     */
    protected bool $isReleaseGroupIntegratorEnabled;

    /**
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManager
     * @param bool $isReleaseGroupIntegratorEnabled
     */
    public function __construct(PackageManagerAdapterInterface $packageManager, bool $isReleaseGroupIntegratorEnabled = false)
    {
        $this->packageManager = $packageManager;
        $this->isReleaseGroupIntegratorEnabled = $isReleaseGroupIntegratorEnabled;
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManagerPackagesDto
     */
    public function fetchPackages(PackageCollection $packageCollection): PackageManagerPackagesDto
    {
        $packagesForRequire = $this->getRequiredPackages($packageCollection);

        $packagesForRequireDev = $this->getRequiredDevPackages($packageCollection);

        $packagesForUpdate = $this->getPackagesForUpdate(
            $packageCollection,
            array_merge($packagesForRequire->toArray(), $packagesForRequireDev->toArray()),
        );

        return new PackageManagerPackagesDto($packagesForRequire, $packagesForRequireDev, $packagesForUpdate);
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
            if ($this->isPackagedShouldBeRequired($package)) {
                $resultCollection->add($package);
            }
        }

        return $resultCollection;
    }

    /**
     * @param \Upgrade\Domain\Entity\Package $package
     *
     * @return bool
     */
    protected function isPackagedShouldBeRequired(Package $package): bool
    {
        $packageConstraint = $this->packageManager->getPackageConstraint($package->getName());

        return !$this->packageManager->isDevPackage($package->getName())
            && ($this->packageManager->getPackageVersion($package->getName()) === null
                || ($packageConstraint !== null && !Semver::satisfies($package->getVersion(), $packageConstraint))
            );
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
            if ($this->isPackageShouldBeRequiredForDev($package)) {
                $resultCollection->add($package);
            }
        }

        return $resultCollection;
    }

    /**
     * @param \Upgrade\Domain\Entity\Package $package
     *
     * @return bool
     */
    protected function isPackageShouldBeRequiredForDev(Package $package): bool
    {
        $packageConstraint = $this->packageManager->getPackageConstraint($package->getName());

        return $this->packageManager->isDevPackage($package->getName())
            && ($packageConstraint !== null && !Semver::satisfies($package->getVersion(), $packageConstraint));
    }

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     * @param array<\Upgrade\Domain\Entity\Package> $requiredPackages
     *
     * @return \Upgrade\Domain\Entity\Collection\PackageCollection
     */
    protected function getPackagesForUpdate(PackageCollection $packageCollection, array $requiredPackages): PackageCollection
    {
        $resultCollection = new PackageCollection();

        foreach ($packageCollection->toArray() as $package) {
            if ($this->isPackageShouldBeUpdated($package, $requiredPackages)) {
                $resultCollection->add($package);
            }
        }

        return $resultCollection;
    }

    /**
     * @param \Upgrade\Domain\Entity\Package $package
     * @param array<\Upgrade\Domain\Entity\Package> $requiredPackages
     *
     * @return bool
     */
    protected function isPackageShouldBeUpdated(Package $package, array $requiredPackages): bool
    {
        return count(array_filter(
            $requiredPackages,
            static fn (Package $requiredPackage): bool => $requiredPackage->getName() === $package->getName()
        )) === 0;
    }
}
