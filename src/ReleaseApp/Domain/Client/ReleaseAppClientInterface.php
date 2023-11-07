<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Domain\Client;

use ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use ReleaseApp\Domain\Client\Request\UpgradeReleaseGroupInstructionsRequest;
use ReleaseApp\Domain\Entities\UpgradeInstruction;
use ReleaseApp\Domain\Entities\UpgradeInstructions;

interface ReleaseAppClientInterface
{
    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest $instructionsRequest
     *
     * @return \ReleaseApp\Domain\Entities\UpgradeInstructions
     */
    public function getUpgradeInstructions(UpgradeInstructionsRequest $instructionsRequest): UpgradeInstructions;

    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeReleaseGroupInstructionsRequest $releaseGroupRequest
     *
     * @return \ReleaseApp\Domain\Entities\UpgradeInstruction
     */
    public function getUpgradeReleaseGroupInstruction(UpgradeReleaseGroupInstructionsRequest $releaseGroupRequest): UpgradeInstruction;
}
