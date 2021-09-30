<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Bridge;

use Upgrader\Business\PackageManagementSystem\Transfer\Collection\ReleaseGroupTransferCollection;
use Upgrader\Business\PackageManagementSystem\Transfer\ReleaseGroupTransfer;
use Upgrader\Business\PackageManager\PackageManagerInterface;
use Upgrader\Business\PackageManager\Response\Collection\PackageManagerResponseCollection;
use Upgrader\Business\PackageManager\Response\PackageManagerResponse;
use Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection;
use Upgrader\Business\Upgrader\Validator\ReleaseGroupSoftValidatorInterface;

class ReleaseGroupTransferBridge implements ReleaseGroupTransferBridgeInterface
{
    /**
     * @var \Upgrader\Business\Upgrader\Validator\ReleaseGroupSoftValidatorInterface
     */
    protected $releaseGroupValidateManager;

    /**
     * @var \Upgrader\Business\Upgrader\Bridge\PackageCollectionBuilderInterface
     */
    protected $packageCollectionBuilder;

    /**
     * @var \Upgrader\Business\PackageManager\PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @param \Upgrader\Business\Upgrader\Validator\ReleaseGroupSoftValidatorInterface $releaseGroupValidateManager
     * @param \Upgrader\Business\Upgrader\Bridge\PackageCollectionBuilderInterface $packageCollectionBuilder
     * @param \Upgrader\Business\PackageManager\PackageManagerInterface $packageManager
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
     * @param \Upgrader\Business\PackageManagementSystem\Transfer\Collection\ReleaseGroupTransferCollection $releaseGroupCollection
     *
     * @return \Upgrader\Business\PackageManager\Response\Collection\PackageManagerResponseCollection
     */
    public function requireCollection(ReleaseGroupTransferCollection $releaseGroupCollection): PackageManagerResponseCollection
    {
        $collection = new PackageManagerResponseCollection();
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
     * @param \Upgrader\Business\PackageManagementSystem\Transfer\ReleaseGroupTransfer $releaseGroup
     *
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    public function require(ReleaseGroupTransfer $releaseGroup): PackageManagerResponse
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

            return new PackageManagerResponse(true, $packagesNameString);
        }

        return $this->requirePackageCollection($filteredPackageCollection);
    }

    /**
     * @param \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection $packageCollection
     *
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    protected function requirePackageCollection(PackageTransferCollection $packageCollection): PackageManagerResponse
    {
        $requireResponse = $this->packageManager->require($packageCollection);
        if (!$requireResponse->isSuccess()) {
            return $requireResponse;
        }

        $packagesNameString = implode(' ', $packageCollection->getNameList());

        return new PackageManagerResponse(true, $packagesNameString);
    }
}
