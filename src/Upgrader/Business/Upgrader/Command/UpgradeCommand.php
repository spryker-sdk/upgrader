<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Command;

use Exception;
use Upgrader\Business\Command\AbstractCommand;
use Upgrader\Business\Command\Response\Collection\CommandResponseCollection;
use Upgrader\Business\Command\Response\CommandResponse;
use Upgrader\Business\DataProvider\DataProvider;
use Upgrader\Business\DataProvider\Entity\ReleaseGroup;
use Upgrader\Business\DataProvider\Request\DataProviderRequest;
use Upgrader\Business\DataProvider\Response\DataProviderResponse;
use Upgrader\Business\PackageManager\Entity\Collection\PackageCollection;
use Upgrader\Business\PackageManager\Entity\Package;
use Upgrader\Business\PackageManager\PackageManagerInterface;

class UpgradeCommand extends AbstractCommand
{
    /**
     * @return string
     */
    public function getCommand(): string
    {
        return 'upgrader upgrade';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'upgrader:upgrade';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'The command for upgrade Spryker packages';
    }

    /**
     * @var \Upgrader\Business\PackageManager\PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @var \Upgrader\Business\DataProvider\DataProvider
     */
    protected $dataProvider;

    /**
     * @param \Upgrader\Business\PackageManager\PackageManagerInterface $packageManager
     * @param \Upgrader\Business\DataProvider\DataProvider $dataProvider
     */
    public function __construct(
        PackageManagerInterface $packageManager,
        DataProvider $dataProvider
    ) {
        $this->dataProvider = $dataProvider;
        $this->packageManager = $packageManager;
    }

    /**
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    public function run(): CommandResponse
    {
        try {
            $dataProviderRequest = $this->createDataProviderRequest();
            $dataProviderResponse = $this->dataProvider->getNotInstalledReleaseGroupList($dataProviderRequest);
            $requireResponseCollection = $this->requirePackages($dataProviderResponse);
        } catch (Exception $exception) {
            return $this->createResponse(false, $exception->getMessage());
        }

        return $this->createResponse($requireResponseCollection->isSuccess(), $requireResponseCollection->getOutput());
    }

    /**
     * @param \Upgrader\Business\DataProvider\Response\DataProviderResponse $providerResponse
     *
     * @return \Upgrader\Business\Command\Response\Collection\CommandResponseCollection
     */
    protected function requirePackages(DataProviderResponse $providerResponse): CommandResponseCollection
    {
        $resultCollection = new CommandResponseCollection();

        /** @var \Upgrader\Business\DataProvider\Entity\ReleaseGroup $releaseGroup */
        foreach ($providerResponse->getReleaseGroupCollection()->toArray() as $releaseGroup) {
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
        if ($releaseGroup->isContainsProjectChanges()) {
            $message = sprintf(
                '%s %s',
                'Release group contains changes on project level. Name:',
                $releaseGroup->getName()
            );

            return $this->createResponse(false, $message);
        }

        if ($releaseGroup->isContainsMajorUpdates()) {
            $message = sprintf(
                '%s %s',
                'Release group contains major changes. Name:',
                $releaseGroup->getName()
            );

            return $this->createResponse(false, $message);
        }

        $packageCollection = $this->createPackageCollection($releaseGroup);

        return $this->packageManager->require($packageCollection);
    }

    /**
     * @param \Upgrader\Business\DataProvider\Entity\ReleaseGroup $releaseGroup
     *
     * @return \Upgrader\Business\PackageManager\Entity\Collection\PackageCollection
     */
    protected function createPackageCollection(ReleaseGroup $releaseGroup): PackageCollection
    {
        $packageCollection = new PackageCollection();

        /** @var \Upgrader\Business\DataProvider\Entity\Module $module */
        foreach ($releaseGroup->getModuleCollection()->toArray() as $module) {
            $installedVersion = $this->packageManager->getPackageVersion($module->getName());
            if (version_compare($installedVersion, $module->getVersion(), '>=')) {
                continue;
            }

            $package = new Package($module->getName(), $module->getVersion());
            $packageCollection->add($package);
        }

        return $packageCollection;
    }

    /**
     * @return \Upgrader\Business\DataProvider\Request\DataProviderRequest
     */
    protected function createDataProviderRequest(): DataProviderRequest
    {
        $projectName = $this->packageManager->getProjectName();
        $composerJson = $this->packageManager->getComposerJsonFile();
        $composerLock = $this->packageManager->getComposerLockFile();

        return new DataProviderRequest($projectName, $composerJson, $composerLock);
    }

    /**
     * @param bool $isSuccess
     * @param string $message
     *
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    protected function createResponse(bool $isSuccess, string $message): CommandResponse
    {
        return new CommandResponse($isSuccess, $this->getName(), $message);
    }
}
