<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Validator;

use Upgrade\Application\Dto\PackageManager\PackageDto;
use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto;

interface PackageSoftValidatorInterface
{
    /**
     * @param \Upgrade\Application\Dto\PackageManager\PackageDto $package
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDto
     */
    public function isValidPackage(PackageDto $package): PackageManagerResponseDto;
}
