<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Validator\ReleaseGroup;

use ReleaseAppClient\Domain\Dto\ReleaseGroupDto;

interface ReleaseGroupValidatorInterface
{
    /**
     * @param \ReleaseAppClient\Domain\Dto\ReleaseGroupDto $releaseGroup
     *
     * @throws \Upgrade\Infrastructure\Exception\UpgraderException
     *
     * @return void
     */
    public function validate(ReleaseGroupDto $releaseGroup): void;
}
