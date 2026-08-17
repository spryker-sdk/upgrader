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
    protected PackageManagerAdapterInterface $packageManager;

    protected bool $isReleaseGroupIntegratorEnabled;

    public function __construct(PackageManagerAdapterInterface $packageManager, bool $isReleaseGroupIntegratorEnabled = false)
    {
        $this->packageManager = $packageManager;
        $this->isReleaseGroupIntegratorEnabled = $isReleaseGroupIntegratorEnabled;
    }

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

    protected function isPackagedShouldBeRequired(Package $package): bool
    {
        $packageConstraint = $this->packageManager->getPackageConstraint($package->getName());

        return !$this->packageManager->isDevPackage($package->getName())
            && ($this->packageManager->getPackageVersion($package->getName()) === null
                || ($packageConstraint !== null && !Semver::satisfies($package->getVersion(), $packageConstraint))
            );
    }

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

    protected function isPackageShouldBeRequiredForDev(Package $package): bool
    {
        $packageConstraint = $this->packageManager->getPackageConstraint($package->getName());

        return $this->packageManager->isDevPackage($package->getName())
            && ($packageConstraint !== null && !Semver::satisfies($package->getVersion(), $packageConstraint));
    }

    /**
     * @param array<\Upgrade\Domain\Entity\Package> $requiredPackages
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
     * @param array<\Upgrade\Domain\Entity\Package> $requiredPackages
     */
    protected function isPackageShouldBeUpdated(Package $package, array $requiredPackages): bool
    {
        return count(array_filter(
            $requiredPackages,
            static fn (Package $requiredPackage): bool => $requiredPackage->getName() === $package->getName(),
        )) === 0;
    }
}
