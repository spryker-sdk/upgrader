<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Validator;

use Upgrade\Application\Dto\ReleaseAppClient\ReleaseGroupDto;
use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto;

interface ReleaseGroupSoftValidatorInterface
{
    /**
     * @param \Upgrade\Application\Dto\ReleaseAppClient\ReleaseGroupDto $releaseGroup
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto
     */
    public function isValidReleaseGroup(ReleaseGroupDto $releaseGroup): PackageManagerResponseDto;
}
