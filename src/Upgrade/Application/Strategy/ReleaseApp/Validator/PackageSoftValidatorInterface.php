<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Validator;

use PackageManager\Domain\Dto\PackageDto;
use PackageManager\Domain\Dto\PackageManagerResponseDto;

interface PackageSoftValidatorInterface
{
    /**
     * @param \PackageManager\Domain\Dto\PackageDto $package
     *
     * @return \PackageManager\Domain\Dto\PackageManagerResponseDto
     */
    public function isValidPackage(PackageDto $package): PackageManagerResponseDto;
}
