<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Mapper;

use Upgrade\Domain\Entity\Collection\PackageCollection;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;

interface PackageCollectionMapperInterface
{
    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection $moduleCollection
     *
     * @return \Upgrade\Domain\Entity\Collection\PackageCollection
     */
    public function mapModuleCollectionToPackageCollection(ModuleDtoCollection $moduleCollection): PackageCollection;

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Domain\Entity\Collection\PackageCollection
     */
    public function filterInvalidPackage(PackageCollection $packageCollection): PackageCollection;

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Domain\Entity\Collection\PackageCollection
     */
    public function getRequiredPackages(PackageCollection $packageCollection): PackageCollection;

    /**
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrade\Domain\Entity\Collection\PackageCollection
     */
    public function getRequiredDevPackages(PackageCollection $packageCollection): PackageCollection;
}
