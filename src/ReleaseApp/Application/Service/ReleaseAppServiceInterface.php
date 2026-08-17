<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Application\Service;

use DateTimeInterface;
use ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use ReleaseApp\Domain\Client\Request\UpgradeReleaseGroupInstructionsRequest;
use ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection;
use ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup;

interface ReleaseAppServiceInterface
{
    public function getNewReleaseGroupsSortedByReleaseDate(UpgradeInstructionsRequest $upgradeInstructionsRequest): UpgradeInstructionsReleaseGroupCollection;

    public function getReleaseGroup(UpgradeReleaseGroupInstructionsRequest $upgradeReleaseGroupInstructionsRequest): UpgradeInstructionsReleaseGroup;

    public function getReleaseHistoryLink(
        ?string $sort = null,
        ?string $direction = null,
        ?DateTimeInterface $releasedFrom = null,
        bool $projectOnly = false
    ): string;
}
