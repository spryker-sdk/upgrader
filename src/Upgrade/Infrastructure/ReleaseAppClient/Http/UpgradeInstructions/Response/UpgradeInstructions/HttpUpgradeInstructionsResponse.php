<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeInstructions\Response\UpgradeInstructions;

use Upgrade\Infrastructure\Exception\UpgraderException;
use Upgrade\Infrastructure\ReleaseAppClient\Http\HttpResponse;

class HttpUpgradeInstructionsResponse extends HttpResponse
{
    /**
     * @var string
     */
    protected const RELEASE_GROUP_KEY = 'release_group';

    /**
     * @return \Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeInstructions\Response\UpgradeInstructions\HttpUpgradeInstructionsReleaseGroup
     * @throws \Upgrade\Infrastructure\Exception\UpgraderException
     *
     */
    public function getReleaseGroup(): HttpUpgradeInstructionsReleaseGroup
    {
        $bodyArray = $this->getBody();

        if (!$bodyArray) {
            throw new UpgraderException('Response body not found');
        }

        return new HttpUpgradeInstructionsReleaseGroup($bodyArray[static::RELEASE_GROUP_KEY]);
    }
}
