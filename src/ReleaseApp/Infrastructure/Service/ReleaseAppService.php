<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Infrastructure\Service;

use ReleaseApp\Application\Service\ReleaseAppService as ApplicationReleaseAppService;
use ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;
use ReleaseApp\Infrastructure\Shared\Mapper\ReleaseGroupDtoCollectionMapper;

class ReleaseAppService implements ReleaseAppServiceInterface
{
    /**
     * @var \ReleaseApp\Application\Service\ReleaseAppService
     */
    protected ApplicationReleaseAppService $releaseApp;

    /**
     * @var \ReleaseApp\Infrastructure\Shared\Mapper\ReleaseGroupDtoCollectionMapper
     */
    protected ReleaseGroupDtoCollectionMapper $releaseGroupDtoCollectionMapper;

    /**
     * @param \ReleaseApp\Application\Service\ReleaseAppService $releaseApp
     * @param \ReleaseApp\Infrastructure\Shared\Mapper\ReleaseGroupDtoCollectionMapper $releaseGroupDtoCollectionMapper
     */
    public function __construct(
        ApplicationReleaseAppService $releaseApp,
        ReleaseGroupDtoCollectionMapper $releaseGroupDtoCollectionMapper
    ) {
        $this->releaseApp = $releaseApp;
        $this->releaseGroupDtoCollectionMapper = $releaseGroupDtoCollectionMapper;
    }

    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest $upgradeAnalysisRequest
     *
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse
     */
    public function getNewReleaseGroups(UpgradeAnalysisRequest $upgradeAnalysisRequest): ReleaseAppResponse
    {
        $releaseGroupCollection = $this->releaseGroupDtoCollectionMapper->mapReleaseGroupTransferCollection(
            $this->releaseApp->getNewReleaseGroupsSortedByReleaseDate($upgradeAnalysisRequest),
        );

        return new ReleaseAppResponse($releaseGroupCollection);
    }
}
