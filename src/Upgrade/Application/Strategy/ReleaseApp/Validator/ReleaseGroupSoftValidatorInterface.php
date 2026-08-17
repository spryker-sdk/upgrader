<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Validator;

use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;
use Upgrade\Application\Dto\ValidatorViolationDto;

interface ReleaseGroupSoftValidatorInterface
{
    public function isValidReleaseGroup(ReleaseGroupDto $releaseGroup): ?ValidatorViolationDto;
}
