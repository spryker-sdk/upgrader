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
     * @param UpgradeAnalysisRequest $request
     * @return UpgradeInstructionsReleaseGroupCollection
     */
    public function getNotInstalledReleaseGroupList(UpgradeAnalysisRequest $request): UpgradeInstructionsReleaseGroupCollection;
}
