<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Application\Service;

use DateTimeInterface;
use ReleaseApp\Application\Configuration\ConfigurationProviderInterface;
use ReleaseApp\Application\Configuration\ReleaseAppConstant;
use ReleaseApp\Domain\Client\ReleaseAppClientInterface;
use ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use ReleaseApp\Domain\Client\Request\UpgradeReleaseGroupInstructionsRequest;
use ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection;
use ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup;

class ReleaseAppService implements ReleaseAppServiceInterface
{
    /**
     * @var \ReleaseApp\Domain\Client\ReleaseAppClientInterface
     */
    protected ReleaseAppClientInterface $releaseAppClient;

    /**
     * @var \ReleaseApp\Application\Configuration\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @param \ReleaseApp\Domain\Client\ReleaseAppClientInterface $releaseAppClient
     * @param \ReleaseApp\Application\Configuration\ConfigurationProviderInterface $configurationProvider
     */
    public function __construct(
        ReleaseAppClientInterface $releaseAppClient,
        ConfigurationProviderInterface $configurationProvider
    ) {
        $this->releaseAppClient = $releaseAppClient;
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest $upgradeInstructionsRequest
     *
     * @return \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection
     */
    public function getNewReleaseGroupsSortedByReleaseDate(
        UpgradeInstructionsRequest $upgradeInstructionsRequest
    ): UpgradeInstructionsReleaseGroupCollection {
        $releaseGroupCollection = $this->releaseAppClient->getUpgradeInstructions($upgradeInstructionsRequest)->getReleaseGroups()->getOnlyWithReleasedDate();

        $upgradeInstructionsReleaseGroupCollection = new UpgradeInstructionsReleaseGroupCollection([
            ...$releaseGroupCollection->getSecurityFixes()->sortByReleasedDate()->toArray(),
            ...$releaseGroupCollection->getNonSecurityFixes()->sortByReleasedDate()->toArray(),
        ]);

        return $upgradeInstructionsReleaseGroupCollection;
    }

    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeReleaseGroupInstructionsRequest $upgradeReleaseGroupInstructionsRequest
     *
     * @return \ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup
     */
    public function getReleaseGroup(UpgradeReleaseGroupInstructionsRequest $upgradeReleaseGroupInstructionsRequest): UpgradeInstructionsReleaseGroup
    {
        $response = $this->releaseAppClient->getUpgradeReleaseGroupInstruction($upgradeReleaseGroupInstructionsRequest);

        return $response->getReleaseGroup();
    }

    /**
     * @param string|null $sort
     * @param string|null $direction
     * @param \DateTimeInterface|null $releasedFrom
     * @param bool $projectOnly
     *
     * @return string
     */
    public function getReleaseHistoryLink(
        ?string $sort = null,
        ?string $direction = null,
        ?DateTimeInterface $releasedFrom = null,
        bool $projectOnly = false
    ): string {
        $queryParams = [];

        if ($sort) {
            $queryParams['sort'] = $sort;
        }
        if ($direction) {
            $queryParams['direction'] = $direction;
        }
        if ($projectOnly) {
            $queryParams['project_only'] = $projectOnly;
        }

        if ($releasedFrom instanceof DateTimeInterface) {
            $queryParams['released_from[day]'] = $releasedFrom->format('d');
            $queryParams['released_from[month]'] = $releasedFrom->format('m');
            $queryParams['released_from[year]'] = $releasedFrom->format('Y');
        }

        return sprintf(
            '%s/%s?%s',
            $this->configurationProvider->getReleaseAppUrl(),
            ReleaseAppConstant::RELEASE_HISTORY_PATH,
            http_build_query($queryParams),
        );
    }
}
