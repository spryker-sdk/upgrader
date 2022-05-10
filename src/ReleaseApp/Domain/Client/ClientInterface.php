<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Domain\Client;

use ReleaseApp\Domain\Entities\UpgradeAnalysis;
use ReleaseApp\Domain\Entities\UpgradeInstructions;
use ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest;
use ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;

interface ClientInterface
{
    /**
     * @param UpgradeAnalysisRequest $upgradeAnalysisRequest
     * @return \ReleaseApp\Domain\Entities\UpgradeAnalysis
     */
    public function getUpgradeAnalysis(UpgradeAnalysisRequest $upgradeAnalysisRequest): UpgradeAnalysis;

    /**
     * @param UpgradeInstructionsRequest $instructionsRequest
     * @return UpgradeInstructions
     */
    public function getUpgradeInstructions(UpgradeInstructionsRequest $instructionsRequest): UpgradeInstructions;

}
