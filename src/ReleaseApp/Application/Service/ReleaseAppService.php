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
    protected ClientInterface $client;

    /**
     * @var \ReleaseApp\Application\Configuration\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @param \ReleaseApp\Domain\Client\ClientInterface $client
     * @param \ReleaseApp\Application\Configuration\ConfigurationProviderInterface $configurationProvider
     */
    public function __construct(ClientInterface $client, ConfigurationProviderInterface $configurationProvider)
    {
        $this->client = $client;
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest $upgradeAnalysisRequest
     *
     * @return \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection
     */
    public function getNewReleaseGroupsSortedByReleaseDate(
        UpgradeAnalysisRequest $upgradeAnalysisRequest
    ): UpgradeInstructionsReleaseGroupCollection {
        $moduleVersionCollection = $this->getModuleVersionCollection($upgradeAnalysisRequest);
        $releaseGroupCollection = $this->getReleaseGroupCollection($moduleVersionCollection);

        return $releaseGroupCollection->getOnlyWithReleasedDate()->sortByReleasedDate();
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
            $request = new UpgradeInstructionsRequest($moduleVersion->getId());
            $response = $this->client->getUpgradeInstructions($request);
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
        $response = $this->client->getUpgradeAnalysis($request);

        return $response->getModuleCollection()
            ->getModulesWithVersions()
            ->getModuleVersions();
    }
}
