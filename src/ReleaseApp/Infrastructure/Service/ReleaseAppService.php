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
    protected ApplicationReleaseAppService $applicationReleaseAppService;

    protected ReleaseGroupDtoCollectionMapper $releaseGroupDtoCollectionMapper;

    /**
     * @param \ReleaseApp\Application\Service\ReleaseAppService $domainReleaseAppClient
     * @param \ReleaseApp\Infrastructure\Shared\Mapper\ReleaseGroupDtoCollectionMapper $releaseGroupDtoCollectionMapper
     */
    public function __construct(
        ApplicationReleaseAppService $domainReleaseAppClient,
        ReleaseGroupDtoCollectionMapper $releaseGroupDtoCollectionMapper
    ) {
        $this->applicationReleaseAppService = $domainReleaseAppClient;
        $this->releaseGroupDtoCollectionMapper = $releaseGroupDtoCollectionMapper;
    }

    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest $request
     *
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse
     */
    public function getNotInstalledReleaseGroupList(UpgradeAnalysisRequest $request): ReleaseAppResponse
    {
        $releaseGroupCollection = $this->releaseGroupDtoCollectionMapper->buildReleaseGroupTransferCollection(
            $this->applicationReleaseAppService->getNotInstalledReleaseGroupList($request),
        );

        return new ReleaseAppResponse($releaseGroupCollection);
    }
}
