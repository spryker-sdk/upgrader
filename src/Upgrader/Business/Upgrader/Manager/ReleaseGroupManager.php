<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Manager;

use Upgrader\Business\Command\Response\Collection\CommandResponseCollection;
use Upgrader\Business\Command\Response\CommandResponse;
use Upgrader\Business\DataProvider\Entity\Collection\ReleaseGroupCollection;
use Upgrader\Business\DataProvider\Entity\ReleaseGroup;
use Upgrader\Business\PackageManager\Entity\Collection\PackageCollection;
use Upgrader\Business\PackageManager\PackageManagerInterface;
use Upgrader\Business\Upgrader\Validator\ReleaseGroupValidateManagerInterface;

class ReleaseGroupManager implements ReleaseGroupManagerInterface
{
    /**
     * @var \Upgrader\Business\Upgrader\Validator\ReleaseGroupValidateManagerInterface
     */
    protected $releaseGroupValidateManager;

    /**
     * @var \Upgrader\Business\Upgrader\Manager\PackageCollectionManagerInterface
     */
    protected $packageCollectionManager;

    /**
     * @var \Upgrader\Business\PackageManager\PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @param \Upgrader\Business\Upgrader\Validator\ReleaseGroupValidateManagerInterface $releaseGroupValidateManager
     * @param \Upgrader\Business\Upgrader\Manager\PackageCollectionManagerInterface $packageCollectionManager
     * @param \Upgrader\Business\PackageManager\PackageManagerInterface $packageManager
     */
    public function __construct(
        ReleaseGroupValidateManagerInterface $releaseGroupValidateManager,
        PackageCollectionManagerInterface $packageCollectionManager,
        PackageManagerInterface $packageManager
    ) {
        $this->releaseGroupValidateManager = $releaseGroupValidateManager;
        $this->packageCollectionManager = $packageCollectionManager;
        $this->packageManager = $packageManager;
    }

    /**
     * @param \Upgrader\Business\DataProvider\Entity\Collection\ReleaseGroupCollection $releaseGroupCollection
     *
     * @return \Upgrader\Business\Command\Response\Collection\CommandResponseCollection
     */
    public function requireCollection(ReleaseGroupCollection $releaseGroupCollection): CommandResponseCollection
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
     * @param \Upgrader\Business\DataProvider\Entity\ReleaseGroup $releaseGroup
     *
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    public function require(ReleaseGroup $releaseGroup): CommandResponse
    {
        $validateResult = $this->releaseGroupValidateManager->isValidReleaseGroup($releaseGroup);
        if (!$validateResult->isSuccess()) {
            return $validateResult;
        }

        $moduleCollection = $releaseGroup->getModuleCollection();
        $packageCollection = $this->packageCollectionManager->createCollectionFromModuleCollection($moduleCollection);
        $packageCollection = $this->packageCollectionManager->filterInvalidPackage($packageCollection);

        return $this->requirePackageCollection($packageCollection);
    }

    /**
     * @param \Upgrader\Business\PackageManager\Entity\Collection\PackageCollection $packageCollection
     *
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    protected function requirePackageCollection(PackageCollection $packageCollection): CommandResponse
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
