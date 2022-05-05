<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Validator;

use PackageManager\Domain\Dto\PackageManagerResponseDto;
use ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection;

interface ThresholdSoftValidatorInterface
{
    /**
     * @param \ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection $moduleDtoCollection
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function isWithInThreshold(ReleaseGroupDtoCollection $moduleDtoCollection): PackageManagerResponseDto;
}