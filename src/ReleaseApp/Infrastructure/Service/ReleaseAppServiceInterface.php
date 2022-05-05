<?php

namespace ReleaseApp\Infrastructure\Service;

use ReleaseApp\Infrastructure\Presentation\Entity\ReleaseAppResponse;
use ReleaseApp\Domain\Entities\UpgradeAnalysis\Request\UpgradeAnalysisRequest;

interface ReleaseAppServiceInterface
{
    /**
     * @param \ReleaseApp\Domain\Entities\UpgradeAnalysis\Request\UpgradeAnalysisRequest $request
     * @return ReleaseAppResponse
     */
    public function getNotInstalledReleaseGroupList(UpgradeAnalysisRequest $request): ReleaseAppResponse;
}
