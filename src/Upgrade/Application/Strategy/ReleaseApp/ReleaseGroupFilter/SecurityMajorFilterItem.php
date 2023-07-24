<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use RuntimeException;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseAppPackageHelper;

class SecurityMajorFilterItem implements ReleaseGroupFilterItemInterface
{
    /**
     * @var \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected PackageManagerAdapterInterface $packageManagerAdapter;

    /**
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManagerAdapter
     */
    public function __construct(PackageManagerAdapterInterface $packageManagerAdapter)
    {
        $this->packageManagerAdapter = $packageManagerAdapter;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroupDto
     *
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto
     */
    public function filter(ReleaseGroupDto $releaseGroupDto): ReleaseGroupDto
    {
        if (!$releaseGroupDto->isSecurity()) {
            return $releaseGroupDto;
        }

        $filteredModuleCollection = new ModuleDtoCollection();
        foreach ($releaseGroupDto->getModuleCollection()->toArray() as $module) {
            $moduleName = ReleaseAppPackageHelper::normalizePackageName($module->getName());
            $installedVersion = (string)$this->packageManagerAdapter->getPackageVersion($moduleName);

            if (
                !$installedVersion ||
                $this->getMajorVersion($installedVersion) === $this->getMajorVersion($module->getVersion())
            ) {
                $filteredModuleCollection->add($module);
            }
        }

        $releaseGroupDto->setModuleCollection($filteredModuleCollection);

        return $releaseGroupDto;
    }

    /**
     * @param string $semanticVersion
     *
     * @throws \RuntimeException
     *
     * @return int
     */
    protected function getMajorVersion(string $semanticVersion): int
    {
        $versionParts = explode('.', $semanticVersion);
        if (!$versionParts || !count($versionParts)) {
            throw new RuntimeException(
                sprintf('Unknown semantic version %s;', $semanticVersion),
            );
        }

        $major = array_shift($versionParts);

        if (!is_int($major) && !is_string($major)) {
            throw new RuntimeException(
                sprintf('Unknown semantic version %s;', $semanticVersion),
            );
        }

        return (int)$major;
    }
}
