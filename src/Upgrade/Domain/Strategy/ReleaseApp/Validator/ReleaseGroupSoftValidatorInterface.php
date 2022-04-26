<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Validator;

use PackageManager\Domain\Dto\PackageManagerResponseDto;
use ReleaseAppClient\Domain\Dto\ReleaseGroupDto;

interface ReleaseGroupSoftValidatorInterface
{
    /**
     * @param \ReleaseAppClient\Domain\Dto\ReleaseGroupDto $releaseGroup
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function isValidReleaseGroup(ReleaseGroupDto $releaseGroup): PackageManagerResponseDto;
}
