<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp;

use Upgrade\Application\Dto\PackageManagementSystem\Collection\ModuleDtoCollection;
use Upgrade\Application\Dto\PackageManagementSystem\Collection\ReleaseGroupDtoCollection;
use Upgrade\Application\Dto\PackageManagementSystem\ModuleDto;
use Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemRequestDto;
use Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemResponseDto;
use Upgrade\Application\Dto\PackageManagementSystem\ReleaseGroupDto;
use Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\HttpClientInterface;
use Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Request\UpgradeAnalysisRequest;
use Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\Collection\UpgradeAnalysisModuleVersionCollection;
use Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Request\UpgradeInstructionsRequest;
use Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\Collection\UpgradeInstructionsReleaseGroupCollection;
use Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\UpgradeInstructionsReleaseGroup;

class ReleaseAppClient implements ReleaseAppClientInterface
{
    /**
     * @var \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\HttpClientInterface
     */
    protected $httpClient;

    /**
     * @param \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemRequestDto $request
     *
     * @return \Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemResponseDto
     */
    public function getNotInstalledReleaseGroupList(PackageManagementSystemRequestDto $request): PackageManagementSystemResponseDto
    {
        $moduleVersionCollection = $this->getModuleVersionCollection($request);
        $releaseGroupCollection = $this->getReleaseGroupCollection($moduleVersionCollection);
        $releaseGroupCollection = $releaseGroupCollection->filterWithoutReleased()->getSortedByReleased();
        $releaseGroupTransferCollection = $this->buildReleaseGroupTransferCollection($releaseGroupCollection);

        return new PackageManagementSystemResponseDto($releaseGroupTransferCollection);
    }

    /**
     * @param \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\Collection\UpgradeInstructionsReleaseGroupCollection $releaseGroupCollection
     *
     * @return \Upgrade\Application\Dto\PackageManagementSystem\Collection\ReleaseGroupDtoCollection
     */
    protected function buildReleaseGroupTransferCollection(
        UpgradeInstructionsReleaseGroupCollection $releaseGroupCollection
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
     * @param \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\UpgradeInstructionsReleaseGroup $releaseGroup
     *
     * @return \Upgrade\Application\Dto\PackageManagementSystem\Collection\ModuleDtoCollection
     */
    protected function buildModuleTransferCollection(UpgradeInstructionsReleaseGroup $releaseGroup): ModuleDtoCollection
    {
        $dataProviderModuleCollection = new ModuleDtoCollection();
        foreach ($releaseGroup->getModuleCollection() as $module) {
            $dataProviderModule = new ModuleDto($module->getName(), $module->getVersion(), $module->getType());
            $dataProviderModuleCollection->add($dataProviderModule);
        }

        return $dataProviderModuleCollection;
    }

    /**
     * @param \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\Collection\UpgradeAnalysisModuleVersionCollection $moduleVersionCollection
     *
     * @return \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\Collection\UpgradeInstructionsReleaseGroupCollection
     */
    protected function getReleaseGroupCollection(
        UpgradeAnalysisModuleVersionCollection $moduleVersionCollection
    ): UpgradeInstructionsReleaseGroupCollection {
        $releaseGroupCollection = new UpgradeInstructionsReleaseGroupCollection();

        foreach ($moduleVersionCollection as $moduleVersion) {
            $request = $this->createUpgradeInstructionsRequest($moduleVersion->getId());

            /** @var \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\UpgradeInstructionsResponse $response */
            $response = $this->httpClient->send($request);

            $releaseGroupCollection->add($response->getReleaseGroup());
        }

        return $releaseGroupCollection;
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemRequestDto $request
     *
     * @return \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\Collection\UpgradeAnalysisModuleVersionCollection
     */
    protected function getModuleVersionCollection(
        PackageManagementSystemRequestDto $request
    ): UpgradeAnalysisModuleVersionCollection {
        $request = $this->createUpgradeAnalysisRequest($request);

        /** @var \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\UpgradeAnalysisResponse $response */
        $response = $this->httpClient->send($request);

        $moduleCollection = $response->getModuleCollection()->getModulesThatContainsAtListOneModuleVersion();
        $moduleVersionCollection = $moduleCollection->getModuleVersionCollection();

        return $moduleVersionCollection;
    }

    /**
     * @param \Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemRequestDto $request
     *
     * @return \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Request\UpgradeAnalysisRequest
     */
    protected function createUpgradeAnalysisRequest(PackageManagementSystemRequestDto $request): UpgradeAnalysisRequest
    {
        return new UpgradeAnalysisRequest(
            $request->getProjectName(),
            $request->getComposerJson(),
            $request->getComposerLock(),
        );
    }

    /**
     * @param int $moduleVersionId
     *
     * @return \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Request\UpgradeInstructionsRequest
     */
    protected function createUpgradeInstructionsRequest(int $moduleVersionId): UpgradeInstructionsRequest
    {
        return new UpgradeInstructionsRequest($moduleVersionId);
    }
}
