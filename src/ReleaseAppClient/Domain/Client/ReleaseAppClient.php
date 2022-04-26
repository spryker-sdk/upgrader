<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Domain\Client;

use ReleaseAppClient\Domain\Dto\Collection\ModuleDtoCollection;
use ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection;
use ReleaseAppClient\Domain\Dto\ModuleDto;
use ReleaseAppClient\Domain\Dto\ReleaseAppClientRequestDto;
use ReleaseAppClient\Domain\Dto\ReleaseAppClientResponseDto;
use ReleaseAppClient\Domain\Dto\ReleaseGroupDto;
use ReleaseAppClient\Domain\Http\HttpClientInterface;
use ReleaseAppClient\Domain\Http\UpgradeAnalysis\Collection\HttpUpgradeAnalysisModuleVersionCollection;
use ReleaseAppClient\Domain\Http\UpgradeAnalysis\Request\HttpUpgradeAnalysisRequest;
use ReleaseAppClient\Domain\Http\UpgradeInstructions\Request\HttpUpgradeInstructionsRequest;
use ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\Collection\HttpUpgradeInstructionsReleaseGroupCollection;
use ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\HttpUpgradeInstructionsReleaseGroup;

class ReleaseAppClient implements ReleaseAppClientInterface
{
    /**
     * @var \ReleaseAppClient\Domain\Http\HttpClientInterface
     */
    protected $httpClient;

    /**
     * @param \ReleaseAppClient\Domain\Http\HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param \ReleaseAppClient\Domain\Dto\ReleaseAppClientRequestDto $request
     *
     * @return \ReleaseAppClient\Domain\Dto\ReleaseAppClientResponseDto
     */
    public function getNotInstalledReleaseGroupList(ReleaseAppClientRequestDto $request): ReleaseAppClientResponseDto
    {
        $moduleVersionCollection = $this->getModuleVersionCollection($request);
        $releaseGroupCollection = $this->getReleaseGroupCollection($moduleVersionCollection);
        $releaseGroupCollection = $releaseGroupCollection->filterWithoutReleased()->getSortedByReleased();
        $releaseGroupTransferCollection = $this->buildReleaseGroupTransferCollection($releaseGroupCollection);

        return new ReleaseAppClientResponseDto($releaseGroupTransferCollection);
    }

    /**
     * @param \ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\Collection\HttpUpgradeInstructionsReleaseGroupCollection $releaseGroupCollection
     *
     * @return \ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection
     */
    protected function buildReleaseGroupTransferCollection(
        HttpUpgradeInstructionsReleaseGroupCollection $releaseGroupCollection
    ): ReleaseGroupDtoCollection {
        $dataProviderReleaseGroupCollection = new ReleaseGroupDtoCollection();

        foreach ($releaseGroupCollection->toArray() as $releaseGroup) {
            $dataProviderReleaseGroup = new ReleaseGroupDto(
                $releaseGroup->getName(),
                $this->buildModuleTransferCollection($releaseGroup),
                $releaseGroup->isContainsProjectChanges(),
            );
            $dataProviderReleaseGroupCollection->add($dataProviderReleaseGroup);
        }

        return $dataProviderReleaseGroupCollection;
    }

    /**
     * @param \ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\HttpUpgradeInstructionsReleaseGroup $releaseGroup
     *
     * @return \ReleaseAppClient\Domain\Dto\Collection\ModuleDtoCollection
     */
    protected function buildModuleTransferCollection(HttpUpgradeInstructionsReleaseGroup $releaseGroup): ModuleDtoCollection
    {
        $dataProviderModuleCollection = new ModuleDtoCollection();
        foreach ($releaseGroup->getModuleCollection()->toArray() as $module) {
            $dataProviderModule = new ModuleDto($module->getName(), $module->getVersion(), $module->getType());
            $dataProviderModuleCollection->add($dataProviderModule);
        }

        return $dataProviderModuleCollection;
    }

    /**
     * @param \ReleaseAppClient\Domain\Http\UpgradeAnalysis\Collection\HttpUpgradeAnalysisModuleVersionCollection $moduleVersionCollection
     *
     * @return \ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\Collection\HttpUpgradeInstructionsReleaseGroupCollection
     */
    protected function getReleaseGroupCollection(
        HttpUpgradeAnalysisModuleVersionCollection $moduleVersionCollection
    ): HttpUpgradeInstructionsReleaseGroupCollection {
        $releaseGroupCollection = new HttpUpgradeInstructionsReleaseGroupCollection();

        foreach ($moduleVersionCollection->toArray() as $moduleVersion) {
            $request = $this->createUpgradeInstructionsRequest($moduleVersion->getId());

            /** @var \ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\HttpUpgradeInstructionsResponse $response */
            $response = $this->httpClient->send($request);

            $releaseGroupCollection->add($response->getReleaseGroup());
        }

        return $releaseGroupCollection;
    }

    /**
     * @param \ReleaseAppClient\Domain\Dto\ReleaseAppClientRequestDto $request
     *
     * @return \ReleaseAppClient\Domain\Http\UpgradeAnalysis\Collection\HttpUpgradeAnalysisModuleVersionCollection
     */
    protected function getModuleVersionCollection(
        ReleaseAppClientRequestDto $request
    ): HttpUpgradeAnalysisModuleVersionCollection {
        $request = $this->createUpgradeAnalysisRequest($request);

        /** @var \ReleaseAppClient\Domain\Http\UpgradeAnalysis\HttpUpgradeAnalysisResponse $response */
        $response = $this->httpClient->send($request);

        $moduleCollection = $response->getModuleCollection()->getModulesThatContainsAtListOneModuleVersion();
        $moduleVersionCollection = $moduleCollection->getModuleVersionCollection();

        return $moduleVersionCollection;
    }

    /**
     * @param \ReleaseAppClient\Domain\Dto\ReleaseAppClientRequestDto $request
     *
     * @return \ReleaseAppClient\Domain\Http\UpgradeAnalysis\Request\HttpUpgradeAnalysisRequest
     */
    protected function createUpgradeAnalysisRequest(ReleaseAppClientRequestDto $request): HttpUpgradeAnalysisRequest
    {
        return new HttpUpgradeAnalysisRequest(
            $request->getProjectName(),
            $request->getComposerJson(),
            $request->getComposerLock(),
        );
    }

    /**
     * @param int $moduleVersionId
     *
     * @return \ReleaseAppClient\Domain\Http\UpgradeInstructions\Request\HttpUpgradeInstructionsRequest
     */
    protected function createUpgradeInstructionsRequest(int $moduleVersionId): HttpUpgradeInstructionsRequest
    {
        return new HttpUpgradeInstructionsRequest($moduleVersionId);
    }
}
