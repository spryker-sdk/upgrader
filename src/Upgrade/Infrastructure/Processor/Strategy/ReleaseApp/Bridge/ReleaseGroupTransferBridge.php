<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Bridge;

use Upgrade\Application\Dto\PackageManagementSystem\Collection\ReleaseGroupDtoCollection;
use Upgrade\Application\Dto\PackageManagementSystem\ReleaseGroupDto;
use Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection;
use Upgrade\Application\Dto\PackageManager\Collection\PackageManagerResponseDtoCollection;
use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto;
use Upgrade\Infrastructure\PackageManager\PackageManagerInterface;
use Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface;

class ReleaseGroupTransferBridge implements ReleaseGroupTransferBridgeInterface
{
    /**
     * @var \Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface
     */
    protected $releaseGroupValidateManager;

    /**
     * @var \Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Bridge\PackageCollectionBuilderInterface
     */
    protected $packageCollectionBuilder;

    /**
     * @var \Upgrade\Infrastructure\PackageManager\PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @param \Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Validator\ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager
     * @param \Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Bridge\PackageCollectionBuilderInterface $packageCollectionBuilder
     * @param \Upgrade\Infrastructure\PackageManager\PackageManagerInterface $packageManager
     */
    public function __construct(
        ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager,
        PackageCollectionBuilderInterface $packageCollectionBuilder,
        PackageManagerInterface $packageManager
    ) {
        $this->releaseGroupValidateManager = $releaseGroupValidateManager;
        $this->packageCollectionBuilder = $packageCollectionBuilder;
        $this->packageManager = $packageManager;
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManagementSystem\Collection\ReleaseGroupDtoCollection $releaseGroupCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\Collection\PackageManagerResponseDtoCollection
     */
    public function requireCollection(ReleaseGroupDtoCollection $releaseGroupCollection): PackageManagerResponseDtoCollection
    {
        $collection = new PackageManagerResponseDtoCollection();
        foreach ($releaseGroupCollection as $releaseGroup) {
            $requireResult = $this->require($releaseGroup);
            $collection->add($requireResult);
            if (!$requireResult->isSuccess()) {
                break;
            }
        }

        return $collection;
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManagementSystem\ReleaseGroupDto $releaseGroup
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto
     */
    public function require(ReleaseGroupDto $releaseGroup): PackageManagerResponseDtoDto
    {
        $validateResult = $this->releaseGroupValidateManager->isValidReleaseGroup($releaseGroup);
        if (!$validateResult->isSuccess()) {
            return $validateResult;
        }

        $moduleCollection = $releaseGroup->getModuleCollection();
        $packageCollection = $this->packageCollectionBuilder->createFromModuleCollection($moduleCollection);
        $filteredPackageCollection = $this->packageCollectionBuilder->filterInvalidPackage($packageCollection);

        if ($filteredPackageCollection->isEmpty()) {
            $packagesNameString = implode(' ', $packageCollection->getNameList());

            return new PackageManagerResponseDtoDto(true, $packagesNameString);
        }

        return $this->requirePackageCollection($filteredPackageCollection);
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManager\Collection\PackageDtoCollection $packageCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto
     */
    protected function requirePackageCollection(PackageDtoCollection $packageCollection): PackageManagerResponseDtoDto
    {
        $requiredPackages = $this->packageCollectionBuilder->getRequiredPackages($packageCollection);
        $requiredDevPackages = $this->packageCollectionBuilder->getRequiredDevPackages($packageCollection);

        if (!$requiredPackages->isEmpty()) {
            $requireResponse = $this->packageManager->require($requiredPackages);
            if (!$requireResponse->isSuccess()) {
                return $requireResponse;
            }
        }

        if (!$requiredDevPackages->isEmpty()) {
            $requireResponse = $this->packageManager->requireDev($requiredDevPackages);
            if (!$requireResponse->isSuccess()) {
                return $requireResponse;
            }
        }

        $packagesNameString = implode(' ', $packageCollection->getNameList());

        return new PackageManagerResponseDtoDto(true, $packagesNameString, $packageCollection->getNameList());
    }
}
