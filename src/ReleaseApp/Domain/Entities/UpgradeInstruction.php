<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Domain\Entities;

use ReleaseApp\Domain\Client\Response\Response;
use ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection;
use Upgrade\Application\Exception\UpgraderException;

class UpgradeInstruction extends Response
{
    /**
     * @var string
     */
    protected const RELEASE_GROUPS_KEY = 'release_groups';

    /**
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
     * @return \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionsReleaseGroupCollection
     */
    public function getReleaseGroups(): UpgradeInstructionsReleaseGroupCollection
    {
        $bodyArray = $this->getBody();

        if (!$bodyArray) {
            throw new UpgraderException('Response body not found');
        }
        $releaseGroupCollection = new UpgradeInstructionsReleaseGroupCollection();

        foreach ($bodyArray[static::RELEASE_GROUPS_KEY] as $releaseGroup) {
            $releaseGroupCollection->add(new UpgradeInstructionsReleaseGroup($releaseGroup));
        }

        return $releaseGroupCollection;
    }
}
