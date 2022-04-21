<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Mapper;

use Upgrade\Application\Dto\ReleaseAppClient\Collection\ModuleDtoCollection;
use Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection;

interface PackageCollectionMapperInterface
{
    /**
     * @param \Upgrade\Application\Dto\ReleaseAppClient\Collection\ModuleDtoCollection $moduleCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection
     */
    public function mapModuleCollectionToPackageCollection(ModuleDtoCollection $moduleCollection): PackageDtoCollection;

    /**
     * @param \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection
     */
    public function filterInvalidPackage(PackageDtoCollection $packageCollection): PackageDtoCollection;

    /**
     * @param \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection
     */
    public function getRequiredPackages(PackageDtoCollection $packageCollection): PackageDtoCollection;

    /**
     * @param \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection
     */
    public function getRequiredDevPackages(PackageDtoCollection $packageCollection): PackageDtoCollection;
}
