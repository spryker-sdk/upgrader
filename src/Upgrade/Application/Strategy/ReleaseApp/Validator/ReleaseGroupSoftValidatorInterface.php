<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Validator;

use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Dto\ResponseDto;

interface ReleaseGroupSoftValidatorInterface
{
    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $releaseGroup
     *
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    public function isValidReleaseGroup(ReleaseGroupDto $releaseGroup): ResponseDto;
}
