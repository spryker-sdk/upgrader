<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Application\Service;

use ReleaseApp\Domain\Client\ReleaseAppClientInterface;
use ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest;
use ReleaseApp\Domain\Client\Request\UpgradeReleaseGroupInstructionsRequest;
use ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection;
use ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup;

class ReleaseAppService implements ReleaseAppServiceInterface
{
    /**
     * @var \ReleaseApp\Domain\Client\ReleaseAppClientInterface
     */
    protected ReleaseAppClientInterface $releaseAppClient;

    /**
     * @param \ReleaseApp\Domain\Client\ReleaseAppClientInterface $releaseAppClient
     */
    public function __construct(ReleaseAppClientInterface $releaseAppClient)
    {
        $this->releaseAppClient = $releaseAppClient;
    }

    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeInstructionsRequest $upgradeInstructionsRequest
     *
     * @return \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection
     */
    public function getNewReleaseGroupsSortedByReleaseDate(
        UpgradeInstructionsRequest $upgradeInstructionsRequest
    ): UpgradeInstructionsReleaseGroupCollection {
        $releaseGroupCollection = $this->releaseAppClient->getUpgradeInstructions($upgradeInstructionsRequest)->getReleaseGroups()->getOnlyWithReleasedDate();

        $upgradeInstructionsReleaseGroupCollection = new UpgradeInstructionsReleaseGroupCollection([
            ...$releaseGroupCollection->getSecurityFixes()->sortByReleasedDate()->toArray(),
            ...$releaseGroupCollection->getNonSecurityFixes()->sortByReleasedDate()->toArray(),
        ]);

        return $upgradeInstructionsReleaseGroupCollection;
    }

    /**
     * @param \ReleaseApp\Domain\Client\Request\UpgradeReleaseGroupInstructionsRequest $upgradeReleaseGroupInstructionsRequest
     *
     * @return \ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup
     */
    public function getReleaseGroup(UpgradeReleaseGroupInstructionsRequest $upgradeReleaseGroupInstructionsRequest): UpgradeInstructionsReleaseGroup
    {
        $response = $this->releaseAppClient->getUpgradeReleaseGroupInstruction($upgradeReleaseGroupInstructionsRequest);

        return $response->getReleaseGroup();
    }
}
