<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Domain\Entities;

use ReleaseApp\Domain\Client\Response\Response;
use ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup;
use Upgrade\Application\Exception\UpgraderException;

class UpgradeInstructions extends Response
{
    /**
     * @var string
     */
    protected const RELEASE_GROUP_KEY = 'release_group';

    /**
     * @return \ReleaseApp\Domain\Entities\UpgradeInstructionsReleaseGroup
     *@throws \Upgrade\Application\Exception\UpgraderException
     *
     */
    public function getReleaseGroup(): UpgradeInstructionsReleaseGroup
    {
        $bodyArray = $this->getBody();

        if (!$bodyArray) {
            throw new UpgraderException('Response body not found');
        }

        return new UpgradeInstructionsReleaseGroup($bodyArray[static::RELEASE_GROUP_KEY]);
    }
}
