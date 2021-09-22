<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Bridge;

use Upgrader\Business\Command\Response\Collection\CommandResponseCollection;
use Upgrader\Business\Command\Response\CommandResponse;
use Upgrader\Business\DataProvider\Entity\ReleaseGroup;
use Upgrader\Business\DataProvider\Response\DataProviderResponse;
use Upgrader\Business\PackageManager\Entity\Collection\PackageCollection;
use Upgrader\Business\PackageManager\Entity\Package;

class PackageManagerBridge
{
    /**
     * @var \Upgrader\Business\Upgrader\Validator\ReleaseGroup\Command\ReleaseGroupValidateCommandInterface
     */
    protected $releaseGroupValidateCommand;

    /**
     * @param \Upgrader\Business\DataProvider\Response\DataProviderResponse $providerResponse
     *
     * @return \Upgrader\Business\Command\Response\Collection\CommandResponseCollection
     */
    public function requirePackageCollection(DataProviderResponse $providerResponse): CommandResponseCollection
    {
        $resultCollection = new CommandResponseCollection();

        foreach ($providerResponse->getReleaseGroupCollection() as $releaseGroup) {
            $validateResult = $this->isValidReleaseGroup($releaseGroup);
            $resultCollection->add($validateResult);
            if (!$validateResult->isSuccess()) {
                break;
            }

            $requireResult = $this->requirePackage($releaseGroup);
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
    protected function requirePackage(ReleaseGroup $releaseGroup): CommandResponse
    {
        $packageCollection = $this->createPackageCollection($releaseGroup);
        $requireResponse = $this->packageManager->require($packageCollection);

        if ($requireResponse->isSuccess()) {
            $message = sprintf('Release group successfully installed. Name: %s', $releaseGroup->getName());

            return $this->createResponse(true, $message);
        }

        return $requireResponse;
    }

    /**
     * @param \Upgrader\Business\DataProvider\Entity\ReleaseGroup $releaseGroup
     *
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    protected function isValidReleaseGroup(ReleaseGroup $releaseGroup): CommandResponse
    {
        $this->releaseGroupValidateCommand->setReleaseGroup($releaseGroup);

        return $this->releaseGroupValidateCommand->run();
    }

    /**
     * @param \Upgrader\Business\DataProvider\Entity\ReleaseGroup $releaseGroup
     *
     * @return \Upgrader\Business\PackageManager\Entity\Collection\PackageCollection
     */
    protected function createPackageCollection(ReleaseGroup $releaseGroup): PackageCollection
    {
        $packageCollection = new PackageCollection();

        foreach ($releaseGroup->getModuleCollection() as $module) {
            $installedVersion = (string)$this->packageManager->getPackageVersion($module->getName());
            if (version_compare($installedVersion, $module->getVersion(), '>=')) {
                continue;
            }

            $package = new Package($module->getName(), $module->getVersion());
            $packageCollection->add($package);
        }

        return $packageCollection;
    }
}
