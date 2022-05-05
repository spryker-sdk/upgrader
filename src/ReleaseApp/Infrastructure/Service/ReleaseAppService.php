<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Infrastructure\Service;

use ReleaseApp\Infrastructure\Presentation\Entity\ReleaseAppResponse;
use ReleaseApp\Infrastructure\Presentation\Mapper\ReleaseGroupDtoCollectionMapper;
use ReleaseApp\Domain\Entities\UpgradeAnalysis\Request\UpgradeAnalysisRequest;

class ReleaseAppService implements ReleaseAppServiceInterface
{
    /**
     * @var \ReleaseApp\Application\Service\ReleaseAppService
     */
    protected ReleaseAppService $domainReleaseAppClient;

    protected ReleaseGroupDtoCollectionMapper $releaseGroupDtoCollectionMapper;

    /**
     * @param ReleaseAppService $domainReleaseAppClient
     * @param \ReleaseApp\Infrastructure\Presentation\Mapper\ReleaseGroupDtoCollectionMapper $releaseGroupDtoCollectionMapper
     */
    public function __construct(ReleaseAppService $domainReleaseAppClient, ReleaseGroupDtoCollectionMapper $releaseGroupDtoCollectionMapper)
    {
        $this->domainReleaseAppClient = $domainReleaseAppClient;
        $this->releaseGroupDtoCollectionMapper = $releaseGroupDtoCollectionMapper;
    }


    /**
     * @param UpgradeAnalysisRequest $request
     * @return ReleaseAppResponse
     */
    public function getNotInstalledReleaseGroupList(UpgradeAnalysisRequest $request): ReleaseAppResponse
    {
        $releaseGroupCollection = $this->releaseGroupDtoCollectionMapper->buildReleaseGroupTransferCollection(
            $this->domainReleaseAppClient->getNotInstalledReleaseGroupList($request)
        );

        return new ReleaseAppResponse($releaseGroupCollection);
    }
}
