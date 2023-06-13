<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Infrastructure\Service;

use ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest;
use ReleaseApp\Domain\Client\Request\UpgradeReleaseGroupInstructionsRequest;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;

interface ReleaseAppServiceInterface
{
    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest $upgradeAnalysisRequest
     *
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse
     */
    public function getNewReleaseGroups(UpgradeAnalysisRequest $upgradeAnalysisRequest): ReleaseAppResponse;

    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeReleaseGroupInstructionsRequest $upgradeReleaseGroupInstructionsRequest
     *
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse
     */
    public function getReleaseGroup(UpgradeReleaseGroupInstructionsRequest $upgradeReleaseGroupInstructionsRequest): ReleaseAppResponse;
}
