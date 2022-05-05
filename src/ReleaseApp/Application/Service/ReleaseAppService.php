<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Application\Service;

use ReleaseApp\Application\Configuration\ConfigurationProviderInterface;
use ReleaseApp\Domain\Repository\ResponseRepositoryInterface;
use ReleaseApp\Domain\Entities\UpgradeAnalysis\Response\Collection\UpgradeAnalysisModuleVersionCollection;
use ReleaseApp\Domain\Entities\UpgradeAnalysis\Request\UpgradeAnalysisRequest;
use ReleaseApp\Domain\Entities\UpgradeInstructions\Request\UpgradeInstructionsRequest;
use ReleaseApp\Domain\Entities\UpgradeInstructions\Response\Collection\UpgradeInstructionsReleaseGroupCollection;

class ReleaseAppService implements ReleaseAppServiceInterface
{
    /**
     * @var \ReleaseApp\Domain\Repository\ResponseRepositoryInterface
     */
    protected ResponseRepositoryInterface $httpClient;

    /**
     * @var \ReleaseApp\Application\Configuration\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @param \ReleaseApp\Domain\Repository\ResponseRepositoryInterface $httpClient
     * @param \ReleaseApp\Application\Configuration\ConfigurationProviderInterface $configurationProvider
     */
    public function __construct(ResponseRepositoryInterface $httpClient, ConfigurationProviderInterface $configurationProvider)
    {
        $this->httpClient = $httpClient;
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @param \ReleaseApp\Domain\Entities\UpgradeAnalysis\Request\UpgradeAnalysisRequest $request
     * @return UpgradeInstructionsReleaseGroupCollection
     */
    public function getNotInstalledReleaseGroupList(UpgradeAnalysisRequest $request): UpgradeInstructionsReleaseGroupCollection
    {
        $moduleVersionCollection = $this->getModuleVersionCollection($request);
        $releaseGroupCollection = $this->getReleaseGroupCollection($moduleVersionCollection);

        return $releaseGroupCollection->filterWithoutReleased()->getSortedByReleased();
    }


    /**
     * @param \ReleaseApp\Domain\Entities\UpgradeAnalysis\Response\Collection\UpgradeAnalysisModuleVersionCollection $moduleVersionCollection
     *
     * @return \ReleaseApp\Domain\Entities\UpgradeInstructions\Response\Collection\UpgradeInstructionsReleaseGroupCollection
     */
    protected function getReleaseGroupCollection(
        UpgradeAnalysisModuleVersionCollection $moduleVersionCollection
    ): UpgradeInstructionsReleaseGroupCollection {
        $releaseGroupCollection = new UpgradeInstructionsReleaseGroupCollection();

        foreach ($moduleVersionCollection->toArray() as $moduleVersion) {
            $request = $this->createUpgradeInstructionsRequest($moduleVersion->getId());

            /** @var \ReleaseApp\Domain\Entities\UpgradeInstructions\Response\UpgradeInstructionsResponse $response */
            $response = $this->httpClient->getResponse($request);

            $releaseGroupCollection->add($response->getReleaseGroup());
        }

        return $releaseGroupCollection;
    }

    /**
     * @param \ReleaseApp\Domain\Entities\UpgradeAnalysis\Request\UpgradeAnalysisRequest $request
     * @return UpgradeAnalysisModuleVersionCollection
     */
    protected function getModuleVersionCollection(
        UpgradeAnalysisRequest $request
    ): UpgradeAnalysisModuleVersionCollection {
        /** @var \ReleaseApp\Domain\Entities\UpgradeAnalysis\Response\UpgradeAnalysisResponse $response */
        $response = $this->httpClient->getResponse($request);

        return $response->getModuleCollection()
            ->getModulesThatContainsAtListOneModuleVersion()
            ->getModuleVersionCollection();
    }

    /**
     * @param int $moduleVersionId
     *
     * @return \ReleaseApp\Domain\Entities\UpgradeInstructions\Request\UpgradeInstructionsRequest
     */
    protected function createUpgradeInstructionsRequest(int $moduleVersionId): UpgradeInstructionsRequest
    {
        return new UpgradeInstructionsRequest($moduleVersionId);
    }
}
