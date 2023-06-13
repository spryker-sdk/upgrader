<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Domain\Client;

use ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest;
use ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use ReleaseApp\Domain\Client\Request\UpgradeReleaseGroupInstructionsRequest;
use ReleaseApp\Domain\Entities\UpgradeAnalysis;
use ReleaseApp\Domain\Entities\UpgradeInstructions;

interface ReleaseAppClientInterface
{
    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest $upgradeAnalysisRequest
     *
     * @return \ReleaseApp\Domain\Entities\UpgradeAnalysis
     */
    public function getUpgradeAnalysis(UpgradeAnalysisRequest $upgradeAnalysisRequest): UpgradeAnalysis;

    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest $instructionsRequest
     *
     * @return \ReleaseApp\Domain\Entities\UpgradeInstructions
     */
    public function getUpgradeInstructions(UpgradeInstructionsRequest $instructionsRequest): UpgradeInstructions;

    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeReleaseGroupInstructionsRequest $releaseGroupRequest
     *
     * @return \ReleaseApp\Domain\Entities\UpgradeInstructions
     */
    public function getUpgradeReleaseGroupInstructions(UpgradeReleaseGroupInstructionsRequest $releaseGroupRequest): UpgradeInstructions;
}
