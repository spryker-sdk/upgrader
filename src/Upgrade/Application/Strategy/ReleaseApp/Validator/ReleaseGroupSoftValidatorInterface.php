<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Validator;

use PackageManager\Domain\Dto\PackageManagerResponseDto;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Dto\ExecutionDto;

interface ReleaseGroupSoftValidatorInterface
{
    /**
     * @param ReleaseGroupDto $releaseGroup
     * @return \Upgrade\Domain\Entity\Step\ExecutionDto
     */
    public function isValidReleaseGroup(ReleaseGroupDto $releaseGroup): ExecutionDto;
}
