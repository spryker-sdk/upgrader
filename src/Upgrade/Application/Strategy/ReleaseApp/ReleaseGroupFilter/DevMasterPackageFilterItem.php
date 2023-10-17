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

class DevMasterPackageFilterItem implements ReleaseGroupFilterItemInterface
{
    /**
     * @var string
     */
    protected const DEV_MASTER_PREFIX = 'dev-master';

    /**
     * @var \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected PackageManagerAdapterInterface $packageManager;

    /**
     * @var array<string>|null
     */
    protected ?array $devMasterModules = null;

    /**
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManager
     */
    public function __construct(PackageManagerAdapterInterface $packageManager)
    {
        $this->packageManager = $packageManager;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroupDto
     *
     * @return \Upgrade\Application\Dto\ReleaseGroupFilterResponseDto
     */
    public function filter(ReleaseGroupDto $releaseGroupDto): ReleaseGroupFilterResponseDto
    {
        $devMasterModules = $this->getProjectDevMasterPackages();

        $filteredModuleCollection = new ModuleDtoCollection();

        foreach ($releaseGroupDto->getModuleCollection()->toArray() as $module) {
            if (in_array(ReleaseAppPackageHelper::normalizePackageName($module->getName()), $devMasterModules, true)) {
                continue;
            }

            $filteredModuleCollection->add($module);
        }

        $releaseGroupDto->setModuleCollection($filteredModuleCollection);

        return new ReleaseGroupFilterResponseDto($releaseGroupDto);
    }

    /**
     * @return array<string>
     */
    protected function getProjectDevMasterPackages(): array
    {
        if ($this->devMasterModules !== null) {
            return $this->devMasterModules;
        }

        $composerJson = $this->packageManager->getComposerJsonFile();

        $packages = array_merge($composerJson['require'], $composerJson['require-dev'] ?? []);

        $this->devMasterModules = [];

        foreach ($packages as $package => $version) {
            if (strpos($version, static::DEV_MASTER_PREFIX) !== 0) {
                continue;
            }

            $this->devMasterModules[] = $package;
        }

        return $this->devMasterModules;
    }
}
