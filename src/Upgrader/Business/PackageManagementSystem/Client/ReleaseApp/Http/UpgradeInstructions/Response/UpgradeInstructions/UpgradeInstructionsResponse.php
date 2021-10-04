<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions;

use Upgrader\Business\Exception\UpgraderException;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpResponse;

class UpgradeInstructionsResponse extends HttpResponse
{
    /**
     * @var string
     */
    protected const RELEASE_GROUP_KEY = 'release_group';

    /**
     * @throws \Upgrader\Business\Exception\UpgraderException
     *
     * @return \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\UpgradeInstructionsReleaseGroup
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
