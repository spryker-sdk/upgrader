<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\ReleaseGroup;

use Upgrade\Application\Dto\ReleaseAppClient\ReleaseGroupDto;

interface ReleaseGroupValidatorInterface
{
    /**
     * @param \Upgrade\Application\Dto\ReleaseAppClient\ReleaseGroupDto $releaseGroup
     *
     * @return void
     *@throws \Upgrade\Infrastructure\Exception\UpgraderException
     *
     */
    public function validate(ReleaseGroupDto $releaseGroup): void;
}
