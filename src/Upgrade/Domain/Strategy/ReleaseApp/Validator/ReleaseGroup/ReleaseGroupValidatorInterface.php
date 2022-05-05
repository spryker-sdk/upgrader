<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Validator\ReleaseGroup;

use ReleaseApp\Infrastructure\Presentation\Entity\ReleaseGroupDto;

interface ReleaseGroupValidatorInterface
{
    /**
     * @param \ReleaseApp\Infrastructure\Presentation\Entity\ReleaseGroupDto $releaseGroup
     *
     * @return void
     *@throws \Upgrade\Infrastructure\Exception\UpgraderException
     *
     */
    public function validate(ReleaseGroupDto $releaseGroup): void;
}
