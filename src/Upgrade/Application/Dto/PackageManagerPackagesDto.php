<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Dto;

use Upgrade\Domain\Entity\Collection\PackageCollection;

class PackageManagerPackagesDto
{
    protected PackageCollection $packagesForRequire;

    protected PackageCollection $packagesForRequireDev;

    protected PackageCollection $packagesForUpdate;

    public function __construct(
        PackageCollection $packagesForRequire,
        PackageCollection $packagesForRequireDev,
        PackageCollection $packagesForUpdate
    ) {
        $this->packagesForRequire = $packagesForRequire;
        $this->packagesForRequireDev = $packagesForRequireDev;
        $this->packagesForUpdate = $packagesForUpdate;
    }

    public function getPackagesForRequire(): PackageCollection
    {
        return $this->packagesForRequire;
    }

    public function getPackagesForRequireDev(): PackageCollection
    {
        return $this->packagesForRequireDev;
    }

    public function getPackagesForUpdate(): PackageCollection
    {
        return $this->packagesForUpdate;
    }
}
