<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions;

use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\HttpResponse;
use Upgrader\Business\Exception\UpgraderException;

class UpgradeInstructionsResponse extends HttpResponse
{
    public const RELEASE_GROUP_KEY = 'release_group';

    /**
     * @throws \Upgrader\Business\Exception\UpgraderException
     *
     * @return \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\UpgradeInstructionsReleaseGroup
     */
    public function getReleaseGroup(): UpgradeInstructionsReleaseGroup
    {
        $bodyArray = $this->getBodyArray();

        if (!$bodyArray) {
            throw new UpgraderException('Response body not found');
        }

        return new UpgradeInstructionsReleaseGroup($bodyArray[self::RELEASE_GROUP_KEY]);
    }
}
