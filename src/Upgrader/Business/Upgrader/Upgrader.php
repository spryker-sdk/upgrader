<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader;

use Symfony\Component\Process\Process;
use Upgrader\Business\Command\CommandInterface;
use Upgrader\Business\Command\CommandRequest;
use Upgrader\Business\Command\CommandResponse;
use Upgrader\Business\Command\CommandResponseList;
use Upgrader\Business\Command\ResultOutput\CommandResultOutput;
use Upgrader\Business\PackageManager\PackageManagerInterface;
use Upgrader\Business\VersionControlSystem\VersionControlSystemInterface;

class Upgrader implements UpgraderInterface
{
    /**
     * @var \Upgrader\Business\Command\CommandInterface[]
     */
    protected $commands = [];

    /**
     * @param \Upgrader\Business\Command\CommandInterface $command
     *
     * @return $this
     */
    public function addCommand(CommandInterface $command)
    {
        $this->commands[] = $command;

        return $this;
    }

    /**
     * @return \Upgrader\Business\Command\CommandInterface[]
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * @param \Upgrader\Business\Command\CommandRequest $commandRequest
     * @return \Upgrader\Business\Command\CommandResponseList
     */
    public function run(CommandRequest $commandRequest): CommandResponseList
    {

        $commandResponseList = new CommandResponseList();
        $commandsList = $commandRequest->getCommandFilterListAsArray();

        /** @var \Evaluator\Business\Command\CommandInterface $command */
        foreach ($this->commands as $command) {
            if ($commandsList !== [] && !in_array($command->getName(), $commandsList)) {
                continue;
            }

            $commandResponse = $command->runCommand();
            $commandResponseList->add($commandResponse);

            if($commandResponseList->getExitCode() == CommandResponse::CODE_ERROR){
                break;
            }
        }

        return $commandResponseList;
    }
}


//class Upgrader implements UpgraderInterface
//{
//    /**
//     * @var \Upgrader\Business\PackageManager\PackageManagerInterface
//     */
//    protected $packageManager;
//
//    /**
//     * @var \Upgrader\Business\VersionControlSystem\VersionControlSystemInterface
//     */
//    protected $versionControlSystem;
//
//    /**
//     * @var \Upgrader\Business\DataProvider\DataProvider
//     */
//    protected $dataProvider;
//
//    /**
//     * @param \Upgrader\Business\PackageManager\PackageManagerInterface $packageManager
//     * @param \Upgrader\Business\VersionControlSystem\VersionControlSystemInterface $versionControlSystem
//     * @param \Upgrader\Business\DataProvider\DataProvider $dataProvider
//     */
//    public function __construct(
//        PackageManagerInterface $packageManager,
//        VersionControlSystemInterface $versionControlSystem,
//        DataProvider $dataProvider
//    ) {
//        $this->dataProvider = $dataProvider;
//        $this->packageManager = $packageManager;
//        $this->versionControlSystem = $versionControlSystem;
//    }
//
//    /**
//     * @return \Upgrader\Business\Command\ResultOutput\Collection\CommandResultOutputCollection
//     */
//    public function upgrade(): CommandResultOutputCollection
//    {
//        $resultCollection = new CommandResultOutputCollection();
//
//        $checkResult = $this->versionControlSystem->checkUncommittedChanges();
//        $resultCollection->add($checkResult);
//
//        if ($checkResult->isSuccess()) {
//            $dataProviderRequest = $this->createDataProviderRequest();
//            $dataProviderResponse = $this->dataProvider->getNotInstalledReleaseGroupList($dataProviderRequest);
//            $requireResultCollection = $this->requirePackages($dataProviderResponse);
//            $resultCollection->addCollection($requireResultCollection);
//        }
//
//        return $resultCollection;
//    }
//
//    /**
//     * @param \Upgrader\Business\DataProvider\Response\DataProviderResponse $providerResponse
//     *
//     * @return \Upgrader\Business\Command\ResultOutput\Collection\CommandResultOutputCollection
//     */
//    protected function requirePackages(DataProviderResponse $providerResponse): CommandResultOutputCollection
//    {
//        $resultCollection = new CommandResultOutputCollection();
//
//        /** @var \Upgrader\Business\DataProvider\Entity\ReleaseGroup $releaseGroup */
//        foreach ($providerResponse->getReleaseGroupCollection()->toArray() as $releaseGroup) {
//            $requireResult = $this->requirePackage($releaseGroup);
//            $resultCollection->add($requireResult);
//
//            if (!$requireResult->isSuccess()) {
//                break;
//            }
//        }
//
//        return $resultCollection;
//    }
//
//    /**
//     * @param \Upgrader\Business\DataProvider\Entity\ReleaseGroup $releaseGroup
//     *
//     * @return \Upgrader\Business\Command\ResultOutput\CommandResultOutput
//     */
//    protected function requirePackage(ReleaseGroup $releaseGroup): CommandResultOutput
//    {
//        if ($releaseGroup->isContainsProjectChanges()) {
//            $message = sprintf(
//                '%s %s',
//                'Release group contains changes on project level. Name:',
//                $releaseGroup->getName()
//            );
//
//            return $this->createErrorResult($message);
//        }
//
//        if ($releaseGroup->isContainsMajorUpdates()) {
//            $message = sprintf(
//                '%s %s',
//                'Release group contains major changes. Name:',
//                $releaseGroup->getName()
//            );
//
//            return $this->createErrorResult($message);
//        }
//
//        $packageCollection = $this->createPackageCollection($releaseGroup);
//
//        return $this->packageManager->require($packageCollection);
//    }
//
//    /**
//     * @param \Upgrader\Business\DataProvider\Entity\ReleaseGroup $releaseGroup
//     *
//     * @return \Upgrader\Business\PackageManager\Entity\Collection\PackageCollection
//     */
//    protected function createPackageCollection(ReleaseGroup $releaseGroup): PackageCollection
//    {
//        $packageCollection = new PackageCollection();
//
//        /** @var \Upgrader\Business\DataProvider\Entity\Module $module */
//        foreach ($releaseGroup->getModuleCollection()->toArray() as $module) {
//            $installedVersion = $this->packageManager->getPackageVersion($module->getName());
//            if (version_compare($installedVersion, $module->getVersion(), '>=')) {
//                continue;
//            }
//
//            $package = new Package($module->getName(), $module->getVersion());
//            $packageCollection->add($package);
//        }
//
//        return $packageCollection;
//    }
//
//    /**
//     * @return \Upgrader\Business\DataProvider\Request\DataProviderRequest
//     */
//    protected function createDataProviderRequest(): DataProviderRequest
//    {
//        $projectName = $this->packageManager->getProjectName();
//        $composerJson = $this->packageManager->getComposerJsonFile();
//        $composerLock = $this->packageManager->getComposerLockFile();
//
//        return new DataProviderRequest($projectName, $composerJson, $composerLock);
//    }
//
//    /**
//     * @param string $message
//     *
//     * @return \Upgrader\Business\Command\ResultOutput\CommandResultOutput
//     */
//    protected function createErrorResult(string $message): CommandResultOutput
//    {
//        return new CommandResultOutput(CommandResultOutput::ERROR_STATUS_CODE, $message);
//    }
//}
