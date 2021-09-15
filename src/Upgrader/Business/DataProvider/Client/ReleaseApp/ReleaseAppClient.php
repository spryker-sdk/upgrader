<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp;

use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\HttpCommunicatorInterface;
use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Request\UpgradeAnalysisRequest;
use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Request\UpgradeInstructionsRequest;
use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\Collection\UpgradeAnalysisModuleVersionCollection;
use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\Collection\UpgradeInstructionsReleaseGroupCollection;
use Upgrader\Business\DataProvider\Entity\Collection\ModuleCollection;
use Upgrader\Business\DataProvider\Entity\Collection\ReleaseGroupCollection;
use Upgrader\Business\DataProvider\Entity\Module;
use Upgrader\Business\DataProvider\Entity\ReleaseGroup;
use Upgrader\Business\DataProvider\Request\DataProviderRequestInterface;
use Upgrader\Business\DataProvider\Response\DataProviderResponse;

class ReleaseAppClient implements ReleaseAppClientInterface
{
    /**
     * @var \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\HttpCommunicatorInterface
     */
    protected $httpCommunicator;

    /**
     * @param \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\HttpCommunicatorInterface $httpCommunicator
     */
    public function __construct(HttpCommunicatorInterface $httpCommunicator)
    {
        $this->httpCommunicator = $httpCommunicator;
    }

    /**
     * @param \Upgrader\Business\DataProvider\Request\DataProviderRequestInterface $request
     *
     * @return \Upgrader\Business\DataProvider\Response\DataProviderResponse
     */
    public function getNotInstalledReleaseGroupList(DataProviderRequestInterface $request): DataProviderResponse
    {
        $moduleVersionCollection = $this->getModuleVersionCollection($request);
        $releaseGroupCollection = $this->getReleaseGroupCollection($moduleVersionCollection);
        $dataProviderRGCollection = $this->buildDataProviderReleaseGroupCollection($releaseGroupCollection);

        return new DataProviderResponse($dataProviderRGCollection);
    }

    /**
     * @param \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\Collection\UpgradeInstructionsReleaseGroupCollection $releaseGroupCollection
     *
     * @return \Upgrader\Business\DataProvider\Entity\Collection\ReleaseGroupCollection
     */
    protected function buildDataProviderReleaseGroupCollection(
        UpgradeInstructionsReleaseGroupCollection $releaseGroupCollection
    ): ReleaseGroupCollection {
        $dataProviderRGCollection = new ReleaseGroupCollection();

        /** @var \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\UpgradeInstructionsReleaseGroup $releaseGroup */
        foreach ($releaseGroupCollection->toArray() as $releaseGroup) {
            $dataProviderModuleCollection = new ModuleCollection();
            /** @var \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\UpgradeInstructionModule $module */
            foreach ($releaseGroup->getModuleCollection()->toArray() as $module) {
                $dataProviderModule = new Module($module->getName(), $module->getVersion(), $module->getType());
                $dataProviderModuleCollection->add($dataProviderModule);
            }

            $dataProviderRg = new ReleaseGroup(
                $releaseGroup->getName(),
                $dataProviderModuleCollection,
                $releaseGroup->isContainsProjectChanges()
            );
            $dataProviderRGCollection->add($dataProviderRg);
        }

        return $dataProviderRGCollection;
    }

    /**
     * @param \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\Collection\UpgradeAnalysisModuleVersionCollection $moduleVersionCollection
     *
     * @return \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\Collection\UpgradeInstructionsReleaseGroupCollection
     */
    protected function getReleaseGroupCollection(
        UpgradeAnalysisModuleVersionCollection $moduleVersionCollection
    ): UpgradeInstructionsReleaseGroupCollection {
        $releaseGroupCollection = new UpgradeInstructionsReleaseGroupCollection();

        /** @var \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\UpgradeAnalysisModuleVersion $moduleVersion */
        foreach ($moduleVersionCollection->toArray() as $moduleVersion) {
            $request = $this->createUpgradeInstructionsRequest($moduleVersion->getId());

            /** @var \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\UpgradeInstructionsResponse $response */
            $response = $this->httpCommunicator->send($request);

            $releaseGroupCollection->add($response->getReleaseGroup());
        }

        return $releaseGroupCollection->getSortedByReleased();
    }

    /**
     * @param \Upgrader\Business\DataProvider\Request\DataProviderRequestInterface $request
     *
     * @return \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\Collection\UpgradeAnalysisModuleVersionCollection
     */
    protected function getModuleVersionCollection(
        DataProviderRequestInterface $request
    ): UpgradeAnalysisModuleVersionCollection {
        $request = $this->createUpgradeAnalysisRequest($request);

        /** @var \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\UpgradeAnalysisResponse $response */
        $response = $this->httpCommunicator->send($request);

        $moduleCollection = $response->getModuleCollection();
        $moduleVersionCollection = $moduleCollection->getModuleVersionCollection();

        return $moduleVersionCollection;
    }

    /**
     * @param \Upgrader\Business\DataProvider\Request\DataProviderRequestInterface $request
     *
     * @return \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Request\UpgradeAnalysisRequest
     */
    protected function createUpgradeAnalysisRequest(DataProviderRequestInterface $request): UpgradeAnalysisRequest
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
     * @return \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Request\UpgradeInstructionsRequest
     */
    protected function createUpgradeInstructionsRequest(int $moduleVersionId): UpgradeInstructionsRequest
    {
        return new UpgradeInstructionsRequest($moduleVersionId);
    }
}
