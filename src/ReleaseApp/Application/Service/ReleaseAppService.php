<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Application\Service;

use ReleaseApp\Application\Configuration\ConfigurationProviderInterface;
use ReleaseApp\Domain\Client\ClientInterface;
use ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest;
use ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use ReleaseApp\Domain\Entities\Collection\UpgradeAnalysisModuleVersionCollection;
use ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection;

class ReleaseAppService implements ReleaseAppServiceInterface
{
    /**
     * @var \ReleaseApp\Domain\Client\ClientInterface
     */
    protected ClientInterface $repository;

    /**
     * @var \ReleaseApp\Application\Configuration\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @param \ReleaseApp\Domain\Client\ClientInterface $httpClient
     * @param \ReleaseApp\Application\Configuration\ConfigurationProviderInterface $configurationProvider
     */
    public function __construct(ClientInterface $httpClient, ConfigurationProviderInterface $configurationProvider)
    {
        $this->repository = $httpClient;
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest $request
     *
     * @return \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection
     */
    public function getNotInstalledReleaseGroupList(UpgradeAnalysisRequest $request): UpgradeInstructionsReleaseGroupCollection
    {
        $moduleVersionCollection = $this->getModuleVersionCollection($request);
        $releaseGroupCollection = $this->getReleaseGroupCollection($moduleVersionCollection);

        return $releaseGroupCollection->filterWithoutReleased()->getSortedByReleased();
    }

    /**
     * @param \ReleaseApp\Domain\Entities\Collection\UpgradeAnalysisModuleVersionCollection $moduleVersionCollection
     *
     * @return \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection
     */
    protected function getReleaseGroupCollection(
        UpgradeAnalysisModuleVersionCollection $moduleVersionCollection
    ): UpgradeInstructionsReleaseGroupCollection {
        $releaseGroupCollection = new UpgradeInstructionsReleaseGroupCollection();

        foreach ($moduleVersionCollection->toArray() as $moduleVersion) {
            $request = $this->createUpgradeInstructionsRequest($moduleVersion->getId());
            $response = $this->repository->getUpgradeInstructions($request);
            $releaseGroupCollection->add($response->getReleaseGroup());
        }

        return $releaseGroupCollection;
    }

    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest $request
     *
     * @return \ReleaseApp\Domain\Entities\Collection\UpgradeAnalysisModuleVersionCollection
     */
    protected function getModuleVersionCollection(
        UpgradeAnalysisRequest $request
    ): UpgradeAnalysisModuleVersionCollection {
        $response = $this->repository->getUpgradeAnalysis($request);

        return $response->getModuleCollection()
            ->getModulesThatContainsAtListOneModuleVersion()
            ->getModuleVersionCollection();
    }

    /**
     * @param int $moduleVersionId
     *
     * @return \ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest
     */
    protected function createUpgradeInstructionsRequest(int $moduleVersionId): UpgradeInstructionsRequest
    {
        return new UpgradeInstructionsRequest($moduleVersionId);
    }
}
