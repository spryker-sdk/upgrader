<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Composer\Fixer;

use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Domain\Entity\Collection\PackageCollection;
use Upgrade\Domain\Entity\Package;

class BackportUpgradeFixer extends AbstractFeaturePackageUpgradeFixer
{
    /**
     * @var bool
     */
    protected const RE_RUN_STEP = false;

    public function __construct(PackageManagerAdapterInterface $packageManager)
    {
        parent::__construct($packageManager);
    }

    public function isApplicable(ReleaseGroupDto $releaseGroup, PackageManagerResponseDto $packageManagerResponseDto): bool
    {
        return !$packageManagerResponseDto->isSuccessful() && $releaseGroup->getBackportModuleCollection()->count();
    }

    public function run(ReleaseGroupDto $releaseGroup, PackageManagerResponseDto $packageManagerResponseDto): ?PackageManagerResponseDto
    {
        return $this->packageManager->require($this->getPackageCollectionWithBackports($releaseGroup));
    }

    protected function getPackageCollectionWithBackports(ReleaseGroupDto $releaseGroupDto): PackageCollection
    {
        $moduleCollection = $releaseGroupDto->getModuleCollection();
        $backportModuleCollection = $releaseGroupDto->getBackportModuleCollection();
        $packages = [];
        foreach ($moduleCollection->toArray() as $moduleDto) {
            $packageName = $moduleDto->getName();
            $packages[$packageName] = new Package($packageName, $moduleDto->getVersion());
        }
        foreach ($backportModuleCollection->toArray() as $moduleDto) {
            $packageName = $moduleDto->getName();
            if (!isset($packages[$packageName])) {
                continue;
            }
            $packages[$packageName] = new Package($packageName, $moduleDto->getVersion());
        }

        return new PackageCollection(array_values($packages));
    }
}
