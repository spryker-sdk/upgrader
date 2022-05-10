<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Validator;

use Upgrade\Domain\Entity\Package;
use PackageManager\Domain\Dto\PackageManagerResponseDto;
use Upgrade\Application\Dto\ExecutionDto;

interface PackageSoftValidatorInterface
{
    /**
     * @param \Upgrade\Domain\Entity\Package $package
     * @return \Upgrade\Domain\Entity\\Upgrade\Domain\Entity\Step\ExecutionDto
     */
    public function isValidPackage(Package $package): ExecutionDto;
}
