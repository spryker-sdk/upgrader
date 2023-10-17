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
    /**
     * @var \Upgrade\Domain\Entity\Collection\PackageCollection
     */
    protected PackageCollection $packagesForRequire;

    /**
     * @var \Upgrade\Domain\Entity\Collection\PackageCollection
     */
    protected PackageCollection $packagesForRequireDev;

    /**
     * @var \Upgrade\Domain\Entity\Collection\PackageCollection
     */
    protected PackageCollection $packagesForUpdate;

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packagesForRequire
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packagesForRequireDev
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packagesForUpdate
     */
    public function __construct(
        PackageCollection $packagesForRequire,
        PackageCollection $packagesForRequireDev,
        PackageCollection $packagesForUpdate
    ) {
        $this->packagesForRequire = $packagesForRequire;
        $this->packagesForRequireDev = $packagesForRequireDev;
        $this->packagesForUpdate = $packagesForUpdate;
    }

    /**
     * @return \Upgrade\Domain\Entity\Collection\PackageCollection
     */
    public function getPackagesForRequire(): PackageCollection
    {
        return $this->packagesForRequire;
    }

    /**
     * @return \Upgrade\Domain\Entity\Collection\PackageCollection
     */
    public function getPackagesForRequireDev(): PackageCollection
    {
        return $this->packagesForRequireDev;
    }

    /**
     * @return \Upgrade\Domain\Entity\Collection\PackageCollection
     */
    public function getPackagesForUpdate(): PackageCollection
    {
        return $this->packagesForUpdate;
    }
}
