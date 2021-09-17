<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
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
