<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Application\Service;

use ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest;
use ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection;

interface ReleaseAppServiceInterface
{
    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest $upgradeAnalysisRequest
     *
     * @return \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection
     */
    public function getNewReleaseGroupsSortedByReleaseDate(UpgradeAnalysisRequest $upgradeAnalysisRequest): UpgradeInstructionsReleaseGroupCollection;
}
