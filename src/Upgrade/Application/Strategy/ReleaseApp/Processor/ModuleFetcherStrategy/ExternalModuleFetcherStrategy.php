<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor\ModuleFetcherStrategy;

use Upgrade\Domain\Entity\Package;

class ExternalModuleFetcherStrategy extends AbstractModuleFetcherStrategy
{
    /**
     * @param \Upgrade\Domain\Entity\Package $package
     *
     * @return bool
     */
    protected function isPackagedShouldBeRequired(Package $package): bool
    {
        return !$this->packageManager->isDevPackage($package->getName());
    }

    /**
     * @param \Upgrade\Domain\Entity\Package $package
     *
     * @return bool
     */
    protected function isPackageShouldBeRequiredForDev(Package $package): bool
    {
        return $this->packageManager->isDevPackage($package->getName());
    }

    /**
     * @param \Upgrade\Domain\Entity\Package $package
     * @param array<\Upgrade\Domain\Entity\Package> $requiredPackages
     *
     * @return bool
     */
    protected function isPackageShouldBeUpdated(Package $package, array $requiredPackages): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isApplicable(): bool
    {
        return !$this->isReleaseGroupIntegratorEnabled;
    }
}
