<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\ReleaseGroupFilterResponseDto;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseAppPackageHelper;

class AlreadyInstalledPackageFilterItem implements ReleaseGroupFilterItemInterface
{
    protected PackageManagerAdapterInterface $packageManagerAdapter;

    public function __construct(PackageManagerAdapterInterface $packageManagerAdapter)
    {
        $this->packageManagerAdapter = $packageManagerAdapter;
    }

    public function filter(ReleaseGroupDto $releaseGroupDto): ReleaseGroupFilterResponseDto
    {
        $filteredModuleCollection = new ModuleDtoCollection();

        foreach ($releaseGroupDto->getModuleCollection()->toArray() as $module) {
            $moduleName = ReleaseAppPackageHelper::normalizePackageName($module->getName());
            $installedVersion = (string)$this->packageManagerAdapter->getPackageVersion($moduleName);

            if (version_compare($installedVersion, $module->getVersion(), '>=')) {
                continue;
            }

            $filteredModuleCollection->add($module);
        }

        $releaseGroupDto->setModuleCollection($filteredModuleCollection);

        return new ReleaseGroupFilterResponseDto($releaseGroupDto);
    }
}
