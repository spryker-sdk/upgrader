<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor\PackageManagerPackagesFetcher;

use Composer\Semver\Semver;
use Upgrade\Domain\Entity\Package;

class InternalPackageManagerPackagesFetcher extends AbstractPackageManagerPackagesFetcher
{
    /**
     * @return bool
     */
    public function isApplicable(): bool
    {
        return $this->isReleaseGroupIntegratorEnabled;
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
