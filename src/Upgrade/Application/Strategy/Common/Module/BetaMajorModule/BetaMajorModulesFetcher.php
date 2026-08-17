<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\Module\BetaMajorModule;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseAppPackageHelper;

class BetaMajorModulesFetcher implements BetaMajorModulesFetcherInterface
{
    protected PackageManagerAdapterInterface $packageManagerAdapter;

    public function __construct(PackageManagerAdapterInterface $packageManagerAdapter)
    {
        $this->packageManagerAdapter = $packageManagerAdapter;
    }

    /**
     * @return array<\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto>
     */
    public function getBetaMajorsNotInstalledInDev(ModuleDtoCollection $moduleDtoCollection): array
    {
        $betaMajors = [];

        foreach ($moduleDtoCollection->getBetaMajors() as $minorModule) {
            $packageName = ReleaseAppPackageHelper::normalizePackageName($minorModule->getName());

            if ($this->packageManagerAdapter->isLockDevPackage($packageName)) {
                continue;
            }

            $betaMajors[] = $minorModule;
        }

        return $betaMajors;
    }
}
