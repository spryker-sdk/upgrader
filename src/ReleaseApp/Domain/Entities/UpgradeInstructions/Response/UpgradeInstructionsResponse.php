<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Domain\Entities\UpgradeInstructions\Response;

use ReleaseApp\Domain\Entities\Response;
use Upgrade\Infrastructure\Exception\UpgraderException;

class UpgradeInstructionsResponse extends Response
{
    /**
     * @var string
     */
    protected const RELEASE_GROUP_KEY = 'release_group';

    /**
     * @return \ReleaseApp\Domain\Entities\UpgradeInstructions\Response\UpgradeInstructionsReleaseGroup
     *@throws \Upgrade\Infrastructure\Exception\UpgraderException
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
