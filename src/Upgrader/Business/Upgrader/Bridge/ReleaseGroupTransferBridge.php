<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Bridge;

use Upgrader\Business\Command\Response\Collection\CommandResponseCollection;
use Upgrader\Business\Command\Response\CommandResponse;
use Upgrader\Business\PackageManagementSystem\Transfer\Collection\ReleaseGroupTransferCollection;
use Upgrader\Business\PackageManagementSystem\Transfer\ReleaseGroupTransfer;
use Upgrader\Business\PackageManager\PackageManagerInterface;
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
     * @return \Upgrader\Business\Command\Response\Collection\CommandResponseCollection
     */
    public function requireCollection(ReleaseGroupTransferCollection $releaseGroupCollection): CommandResponseCollection
    {
        $resultCollection = new CommandResponseCollection();

        foreach ($releaseGroupCollection as $releaseGroup) {
            $requireResult = $this->require($releaseGroup);
            $resultCollection->add($requireResult);
            if (!$requireResult->isSuccess()) {
                break;
            }
        }

        return $resultCollection;
    }

    /**
     * @param \Upgrader\Business\PackageManagementSystem\Transfer\ReleaseGroupTransfer $releaseGroup
     *
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    public function require(ReleaseGroupTransfer $releaseGroup): CommandResponse
    {
        $validateResult = $this->releaseGroupValidateManager->isValidReleaseGroup($releaseGroup);
        if (!$validateResult->isSuccess()) {
            return $validateResult;
        }

        $moduleCollection = $releaseGroup->getModuleCollection();
        $packageCollection = $this->packageCollectionBuilder->createCollectionFromModuleCollection($moduleCollection);
        $packageCollection = $this->packageCollectionBuilder->filterInvalidPackage($packageCollection);

        return $this->requirePackageCollection($packageCollection);
    }

    /**
     * @param \Upgrader\Business\PackageManager\Transfer\Collection\PackageTransferCollection $packageCollection
     *
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    protected function requirePackageCollection(PackageTransferCollection $packageCollection): CommandResponse
    {
        $requireResponse = $this->packageManager->require($packageCollection);
        if (!$requireResponse->isSuccess()) {
            return $requireResponse;
        }

        $packagesNameString = implode(' ', $packageCollection->getNameList());
        $message = sprintf('Installed %s', $packagesNameString);

        return new CommandResponse(true, $message);
    }
}
