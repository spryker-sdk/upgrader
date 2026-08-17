<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Infrastructure\Service;

use DateTimeInterface;
use ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use ReleaseApp\Domain\Client\Request\UpgradeReleaseGroupInstructionsRequest;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;

interface ReleaseAppServiceInterface
{
    public function getNewReleaseGroups(UpgradeInstructionsRequest $upgradeInstructionsRequest): ReleaseAppResponse;

    public function getReleaseGroup(UpgradeReleaseGroupInstructionsRequest $upgradeReleaseGroupInstructionsRequest): ReleaseAppResponse;

    public function getReleaseHistoryLink(
        ?string $sort = null,
        ?string $direction = null,
        ?DateTimeInterface $releasedFrom = null,
        bool $projectOnly = false
    ): string;
}
