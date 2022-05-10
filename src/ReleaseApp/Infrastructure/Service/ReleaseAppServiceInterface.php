<?php

namespace ReleaseApp\Infrastructure\Service;

use ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;
use ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest;

interface ReleaseAppServiceInterface
{
    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest $request
     * @return ReleaseAppResponse
     */
    public function getNotInstalledReleaseGroupList(UpgradeAnalysisRequest $request): ReleaseAppResponse;
}
