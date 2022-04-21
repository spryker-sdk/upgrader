<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\ReleaseAppClient;

use Upgrade\Application\Dto\ReleaseAppClient\Collection\ModuleDtoCollection;
use Upgrade\Application\Dto\ReleaseAppClient\Collection\ReleaseGroupDtoCollection;
use Upgrade\Application\Dto\ReleaseAppClient\ModuleDto;
use Upgrade\Application\Dto\ReleaseAppClient\ReleaseAppClientRequestDto;
use Upgrade\Application\Dto\ReleaseAppClient\ReleaseAppClientResponseDto;
use Upgrade\Application\Dto\ReleaseAppClient\ReleaseGroupDto;
use Upgrade\Infrastructure\ReleaseAppClient\Http\HttpClientInterface;
use Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Request\HttpUpgradeAnalysisRequest;
use Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Response\Collection\HttpUpgradeAnalysisModuleVersionCollection;
use Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeInstructions\Request\HttpUpgradeInstructionsRequest;
use Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeInstructions\Response\UpgradeInstructions\Collection\HttpUpgradeInstructionsReleaseGroupCollection;
use Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeInstructions\Response\UpgradeInstructions\HttpUpgradeInstructionsReleaseGroup;

class ReleaseAppClient implements ReleaseAppClientInterface
{
    /**
     * @var \Upgrade\Infrastructure\ReleaseAppClient\Http\HttpClientInterface
     */
    protected $httpClient;

    /**
     * @param \Upgrade\Infrastructure\ReleaseAppClient\Http\HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param \Upgrade\Application\Dto\ReleaseAppClient\ReleaseAppClientRequestDto $request
     *
     * @return \Upgrade\Application\Dto\ReleaseAppClient\ReleaseAppClientResponseDto
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
     * @param \Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeInstructions\Response\UpgradeInstructions\Collection\HttpUpgradeInstructionsReleaseGroupCollection $releaseGroupCollection
     *
     * @return \Upgrade\Application\Dto\ReleaseAppClient\Collection\ReleaseGroupDtoCollection
     */
    protected function buildReleaseGroupTransferCollection(
        HttpUpgradeInstructionsReleaseGroupCollection $releaseGroupCollection
    ): ReleaseGroupDtoCollection {
        $dataProviderReleaseGroupCollection = new ReleaseGroupDtoCollection();

        foreach ($releaseGroupCollection as $releaseGroup) {
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
     * @param \Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeInstructions\Response\UpgradeInstructions\HttpUpgradeInstructionsReleaseGroup $releaseGroup
     *
     * @return \Upgrade\Application\Dto\ReleaseAppClient\Collection\ModuleDtoCollection
     */
    protected function buildModuleTransferCollection(HttpUpgradeInstructionsReleaseGroup $releaseGroup): ModuleDtoCollection
    {
        $dataProviderModuleCollection = new ModuleDtoCollection();
        foreach ($releaseGroup->getModuleCollection() as $module) {
            $dataProviderModule = new ModuleDto($module->getName(), $module->getVersion(), $module->getType());
            $dataProviderModuleCollection->add($dataProviderModule);
        }

        return $dataProviderModuleCollection;
    }

    /**
     * @param \Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Response\Collection\HttpUpgradeAnalysisModuleVersionCollection $moduleVersionCollection
     *
     * @return \Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeInstructions\Response\UpgradeInstructions\Collection\HttpUpgradeInstructionsReleaseGroupCollection
     */
    protected function getReleaseGroupCollection(
        HttpUpgradeAnalysisModuleVersionCollection $moduleVersionCollection
    ): HttpUpgradeInstructionsReleaseGroupCollection {
        $releaseGroupCollection = new HttpUpgradeInstructionsReleaseGroupCollection();

        foreach ($moduleVersionCollection as $moduleVersion) {
            $request = $this->createUpgradeInstructionsRequest($moduleVersion->getId());

            /** @var \Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeInstructions\Response\UpgradeInstructions\HttpUpgradeInstructionsResponse $response */
            $response = $this->httpClient->send($request);

            $releaseGroupCollection->add($response->getReleaseGroup());
        }

        return $releaseGroupCollection;
    }

    /**
     * @param \Upgrade\Application\Dto\ReleaseAppClient\ReleaseAppClientRequestDto $request
     *
     * @return \Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Response\Collection\HttpUpgradeAnalysisModuleVersionCollection
     */
    protected function getModuleVersionCollection(
        ReleaseAppClientRequestDto $request
    ): HttpUpgradeAnalysisModuleVersionCollection {
        $request = $this->createUpgradeAnalysisRequest($request);

        /** @var \Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Response\HttpUpgradeAnalysisResponse $response */
        $response = $this->httpClient->send($request);

        $moduleCollection = $response->getModuleCollection()->getModulesThatContainsAtListOneModuleVersion();
        $moduleVersionCollection = $moduleCollection->getModuleVersionCollection();

        return $moduleVersionCollection;
    }

    /**
     * @param \Upgrade\Application\Dto\ReleaseAppClient\ReleaseAppClientRequestDto $request
     *
     * @return \Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Request\HttpUpgradeAnalysisRequest
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
     * @return \Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeInstructions\Request\HttpUpgradeInstructionsRequest
     */
    protected function createUpgradeInstructionsRequest(int $moduleVersionId): HttpUpgradeInstructionsRequest
    {
        return new HttpUpgradeInstructionsRequest($moduleVersionId);
    }
}
