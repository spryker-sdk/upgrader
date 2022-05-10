<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Validator;

use PackageManager\Domain\Dto\PackageManagerResponseDto;
use ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection;
use Upgrade\Application\Dto\ExecutionDto;

interface ThresholdSoftValidatorInterface
{
    /**
     * @param ReleaseGroupDtoCollection $moduleDtoCollection
     * @return ExecutionDto
     */
    public function isWithInThreshold(ReleaseGroupDtoCollection $moduleDtoCollection): ExecutionDto;
}
