<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Mapper;

use PackageManager\Domain\Dto\Collection\PackageDtoCollection;
use ReleaseApp\Infrastructure\Presentation\Entity\Collection\ModuleDtoCollection;

interface PackageCollectionMapperInterface
{
    /**
     * @param \ReleaseApp\Infrastructure\Presentation\Entity\Collection\ModuleDtoCollection $moduleCollection
     *
     * @return \PackageManager\Domain\Dto\Collection\PackageDtoCollection
     */
    public function mapModuleCollectionToPackageCollection(ModuleDtoCollection $moduleCollection): PackageDtoCollection;

    /**
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection $packageCollection
     *
     * @return \PackageManager\Domain\Dto\Collection\PackageDtoCollection
     */
    public function filterInvalidPackage(PackageDtoCollection $packageCollection): PackageDtoCollection;

    /**
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection $packageCollection
     *
     * @return \PackageManager\Domain\Dto\Collection\PackageDtoCollection
     */
    public function getRequiredPackages(PackageDtoCollection $packageCollection): PackageDtoCollection;

    /**
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection $packageCollection
     *
     * @return \PackageManager\Domain\Dto\Collection\PackageDtoCollection
     */
    public function getRequiredDevPackages(PackageDtoCollection $packageCollection): PackageDtoCollection;
}
