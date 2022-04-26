<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Domain\Http\UpgradeInstructions\Response;

use ReleaseAppClient\Domain\Http\HttpResponse;
use Upgrade\Infrastructure\Exception\UpgraderException;

class HttpUpgradeInstructionsResponse extends HttpResponse
{
    /**
     * @var string
     */
    protected const RELEASE_GROUP_KEY = 'release_group';

    /**
     * @throws \Upgrade\Infrastructure\Exception\UpgraderException
     *
     * @return \ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\HttpUpgradeInstructionsReleaseGroup
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
