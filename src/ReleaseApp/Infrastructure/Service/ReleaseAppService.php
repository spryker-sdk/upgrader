<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Infrastructure\Service;

use DateTimeInterface;
use ReleaseApp\Application\Service\ReleaseAppService as ApplicationReleaseAppService;
use ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use ReleaseApp\Domain\Client\Request\UpgradeReleaseGroupInstructionsRequest;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;
use ReleaseApp\Infrastructure\Shared\Mapper\ReleaseGroupDtoCollectionMapper;

class ReleaseAppService implements ReleaseAppServiceInterface
{
    protected ApplicationReleaseAppService $releaseApp;

    protected ReleaseGroupDtoCollectionMapper $releaseGroupDtoCollectionMapper;

    public function __construct(
        ApplicationReleaseAppService $releaseApp,
        ReleaseGroupDtoCollectionMapper $releaseGroupDtoCollectionMapper
    ) {
        $this->releaseApp = $releaseApp;
        $this->releaseGroupDtoCollectionMapper = $releaseGroupDtoCollectionMapper;
    }

    public function getNewReleaseGroups(UpgradeInstructionsRequest $upgradeInstructionsRequest): ReleaseAppResponse
    {
        $releaseGroupCollection = $this->releaseGroupDtoCollectionMapper->mapReleaseGroupTransferCollection(
            $this->releaseApp->getNewReleaseGroupsSortedByReleaseDate($upgradeInstructionsRequest),
        );

        return new ReleaseAppResponse($releaseGroupCollection);
    }

    public function getReleaseGroup(UpgradeReleaseGroupInstructionsRequest $upgradeReleaseGroupInstructionsRequest): ReleaseAppResponse
    {
        $releaseGroupCollection = $this->releaseGroupDtoCollectionMapper->mapReleaseGroupDtoIntoCollection(
            $this->releaseApp->getReleaseGroup($upgradeReleaseGroupInstructionsRequest),
        );

        return new ReleaseAppResponse($releaseGroupCollection);
    }

    public function getReleaseHistoryLink(
        ?string $sort = null,
        ?string $direction = null,
        ?DateTimeInterface $releasedFrom = null,
        bool $projectOnly = false
    ): string {
        return $this->releaseApp->getReleaseHistoryLink($sort, $direction, $releasedFrom, $projectOnly);
    }
}
