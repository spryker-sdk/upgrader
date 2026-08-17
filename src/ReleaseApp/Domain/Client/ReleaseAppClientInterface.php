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
    public function getUpgradeInstructions(UpgradeInstructionsRequest $instructionsRequest): UpgradeInstructions;

    public function getUpgradeReleaseGroupInstruction(UpgradeReleaseGroupInstructionsRequest $releaseGroupRequest): UpgradeInstruction;
}
