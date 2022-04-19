<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Validator;

use Upgrade\Application\Dto\PackageManagementSystem\ReleaseGroupDto;
use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto;

interface ReleaseGroupSoftValidatorInterface
{
    /**
     * @param \Upgrade\Application\Dto\PackageManagementSystem\ReleaseGroupDto $releaseGroup
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto
     */
    public function isValidReleaseGroup(ReleaseGroupDto $releaseGroup): PackageManagerResponseDtoDto;
}
