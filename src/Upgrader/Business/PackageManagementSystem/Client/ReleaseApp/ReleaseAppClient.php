<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem\Client\ReleaseApp;

use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpClientInterface;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Request\UpgradeAnalysisRequest;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\Collection\UpgradeAnalysisModuleVersionCollection;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Request\UpgradeInstructionsRequest;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\Collection\UpgradeInstructionsReleaseGroupCollection;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\UpgradeInstructionsReleaseGroup;
use Upgrader\Business\PackageManagementSystem\Request\PackageManagementSystemRequestInterface;
use Upgrader\Business\PackageManagementSystem\Response\PackageManagementSystemResponse;
use Upgrader\Business\PackageManagementSystem\Transfer\Collection\ModuleTransferCollection;
use Upgrader\Business\PackageManagementSystem\Transfer\Collection\ReleaseGroupTransferCollection;
use Upgrader\Business\PackageManagementSystem\Transfer\ModuleTransfer;
use Upgrader\Business\PackageManagementSystem\Transfer\ReleaseGroupTransfer;

class ReleaseAppClient implements ReleaseAppClientInterface
{
    /**
     * @var \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpClientInterface
     */
    protected $httpClient;

    /**
     * @param \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param \Upgrader\Business\PackageManagementSystem\Request\PackageManagementSystemRequestInterface $request
     *
     * @return \Upgrader\Business\PackageManagementSystem\Response\PackageManagementSystemResponse
     */
    public function getNotInstalledReleaseGroupList(PackageManagementSystemRequestInterface $request): PackageManagementSystemResponse
    {
        $moduleVersionCollection = $this->getModuleVersionCollection($request);
        $releaseGroupCollection = $this->getReleaseGroupCollection($moduleVersionCollection);
        $releaseGroupCollection = $releaseGroupCollection->filterWithoutReleased()->getSortedByReleased();
        $releaseGroupTransferCollection = $this->buildReleaseGroupTransferCollection($releaseGroupCollection);

        return new PackageManagementSystemResponse($releaseGroupTransferCollection);
    }

    /**
     * @param \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\Collection\UpgradeInstructionsReleaseGroupCollection $releaseGroupCollection
     *
     * @return \Upgrader\Business\PackageManagementSystem\Transfer\Collection\ReleaseGroupTransferCollection
     */
    protected function buildReleaseGroupTransferCollection(
        UpgradeInstructionsReleaseGroupCollection $releaseGroupCollection
    ): ReleaseGroupTransferCollection {
        $dataProviderReleaseGroupCollection = new ReleaseGroupTransferCollection();

        foreach ($releaseGroupCollection as $releaseGroup) {
            $dataProviderReleaseGroup = new ReleaseGroupTransfer(
                $releaseGroup->getName(),
                $this->buildModuleTransferCollection($releaseGroup),
                $releaseGroup->isContainsProjectChanges()
            );
            $dataProviderReleaseGroupCollection->add($dataProviderReleaseGroup);
        }

        return $dataProviderReleaseGroupCollection;
    }

    /**
     * @param \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\UpgradeInstructionsReleaseGroup $releaseGroup
     *
     * @return \Upgrader\Business\PackageManagementSystem\Transfer\Collection\ModuleTransferCollection
     */
    protected function buildModuleTransferCollection(UpgradeInstructionsReleaseGroup $releaseGroup): ModuleTransferCollection
    {
        $dataProviderModuleCollection = new ModuleTransferCollection();
        foreach ($releaseGroup->getModuleCollection() as $module) {
            $dataProviderModule = new ModuleTransfer($module->getName(), $module->getVersion(), $module->getType());
            $dataProviderModuleCollection->add($dataProviderModule);
        }

        return $dataProviderModuleCollection;
    }

    /**
     * @param \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\Collection\UpgradeAnalysisModuleVersionCollection $moduleVersionCollection
     *
     * @return \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\Collection\UpgradeInstructionsReleaseGroupCollection
     */
    protected function getReleaseGroupCollection(
        UpgradeAnalysisModuleVersionCollection $moduleVersionCollection
    ): UpgradeInstructionsReleaseGroupCollection {
        $releaseGroupCollection = new UpgradeInstructionsReleaseGroupCollection();

        foreach ($moduleVersionCollection as $moduleVersion) {
            $request = $this->createUpgradeInstructionsRequest($moduleVersion->getId());

            /** @var \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\UpgradeInstructionsResponse $response */
            $response = $this->httpClient->send($request);

            $releaseGroupCollection->add($response->getReleaseGroup());
        }

        return $releaseGroupCollection;
    }

    /**
     * @param \Upgrader\Business\PackageManagementSystem\Request\PackageManagementSystemRequestInterface $request
     *
     * @return \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\Collection\UpgradeAnalysisModuleVersionCollection
     */
    protected function getModuleVersionCollection(
        PackageManagementSystemRequestInterface $request
    ): UpgradeAnalysisModuleVersionCollection {
        $request = $this->createUpgradeAnalysisRequest($request);

        /** @var \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\UpgradeAnalysisResponse $response */
        $response = $this->httpClient->send($request);

        $moduleCollection = $response->getModuleCollection()->getModulesThatContainsAtListOneModuleVersion();
        $moduleVersionCollection = $moduleCollection->getModuleVersionCollection();

        return $moduleVersionCollection;
    }

    /**
     * @param \Upgrader\Business\PackageManagementSystem\Request\PackageManagementSystemRequestInterface $request
     *
     * @return \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Request\UpgradeAnalysisRequest
     */
    protected function createUpgradeAnalysisRequest(PackageManagementSystemRequestInterface $request): UpgradeAnalysisRequest
    {
        return new UpgradeAnalysisRequest(
            $request->getProjectName(),
            $request->getComposerJson(),
            $request->getComposerLock()
        );
    }

    /**
     * @param int $moduleVersionId
     *
     * @return \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Request\UpgradeInstructionsRequest
     */
    protected function createUpgradeInstructionsRequest(int $moduleVersionId): UpgradeInstructionsRequest
    {
        return new UpgradeInstructionsRequest($moduleVersionId);
    }
}
