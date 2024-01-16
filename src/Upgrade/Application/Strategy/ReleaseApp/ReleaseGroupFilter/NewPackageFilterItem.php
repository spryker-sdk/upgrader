<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\ReleaseGroupFilter;

use ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection;
use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\ReleaseGroupFilterResponseDto;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\ReleaseApp\ReleaseAppPackageHelper;

class NewPackageFilterItem implements ReleaseGroupFilterItemInterface
{
    /**
     * @var \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected PackageManagerAdapterInterface $packageManagerAdapter;

    /**
     * @var \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @var bool
     */
    protected bool $isFeatureToDevMasterEnabled;

    /**
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManagerAdapter
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     * @param bool $isFeatureToDevMasterEnabled
     */
    public function __construct(
        PackageManagerAdapterInterface $packageManagerAdapter,
        ConfigurationProviderInterface $configurationProvider,
        bool $isFeatureToDevMasterEnabled = false
    ) {
        $this->packageManagerAdapter = $packageManagerAdapter;
        $this->configurationProvider = $configurationProvider;
        $this->isFeatureToDevMasterEnabled = $isFeatureToDevMasterEnabled;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroupDto
     *
     * @return \Upgrade\Application\Dto\ReleaseGroupFilterResponseDto
     */
    public function filter(ReleaseGroupDto $releaseGroupDto): ReleaseGroupFilterResponseDto
    {
        if (!$this->configurationProvider->isPackageUpgradeOnly()) {
            return new ReleaseGroupFilterResponseDto($releaseGroupDto);
        }

        $approvedModuleCollection = new ModuleDtoCollection();
        $proposedModuleCollection = new ModuleDtoCollection();

        foreach ($releaseGroupDto->getModuleCollection()->toArray() as $module) {
            $moduleName = ReleaseAppPackageHelper::normalizePackageName($module->getName());

            if (
                !$this->packageManagerAdapter->getPackageVersion($moduleName) &&
                (
                    !$this->configurationProvider->getReleaseGroupId() || !$this->isFeatureToDevMasterEnabled || !$this->existFeaturePackages($module)
                )
            ) {
                $proposedModuleCollection->add($module);

                continue;
            }

            $approvedModuleCollection->add($module);
        }

        $releaseGroupDto->setModuleCollection($approvedModuleCollection);

        return new ReleaseGroupFilterResponseDto($releaseGroupDto, $proposedModuleCollection);
    }

    /**
     * Checks if some feature packages from module exists in demoshop.
     *
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ModuleDto $module
     *
     * @return bool
     */
    protected function existFeaturePackages(ModuleDto $module): bool
    {
        return $module->getFeaturePackages() &&
            array_filter(
                array_keys($module->getFeaturePackages()),
                function (string $featurePackage): bool {
                    return (bool)$this->packageManagerAdapter->getPackageVersion(ReleaseAppPackageHelper::normalizePackageName($featurePackage));
                },
            );
    }
}
