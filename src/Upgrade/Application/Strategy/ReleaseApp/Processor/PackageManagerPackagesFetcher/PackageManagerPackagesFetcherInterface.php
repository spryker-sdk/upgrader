<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor\PackageManagerPackagesFetcher;

use Upgrade\Application\Dto\PackageManagerPackagesDto;
use Upgrade\Domain\Entity\Collection\PackageCollection;

interface PackageManagerPackagesFetcherInterface
{
    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManagerPackagesDto
     */
    public function fetchPackages(PackageCollection $packageCollection): PackageManagerPackagesDto;

    /**
     * @return bool
     */
    public function isApplicable(): bool;
}
