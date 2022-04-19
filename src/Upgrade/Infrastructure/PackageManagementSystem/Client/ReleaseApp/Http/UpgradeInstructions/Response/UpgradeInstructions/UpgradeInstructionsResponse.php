<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions;

use Upgrade\Infrastructure\Exception\UpgraderException;
use Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\HttpResponse;

class UpgradeInstructionsResponse extends HttpResponse
{
    /**
     * @var string
     */
    protected const RELEASE_GROUP_KEY = 'release_group';

    /**
     * @throws \Upgrade\Infrastructure\Exception\UpgraderException
     *
     * @return \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\UpgradeInstructionsReleaseGroup
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
