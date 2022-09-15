<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Validator;

use Upgrade\Application\Dto\ResponseDto;
use Upgrade\Domain\Entity\Package;

interface PackageSoftValidatorInterface
{
    /**
     * @param \Upgrade\Domain\Entity\Package $package
     *
     * @return \Upgrade\Application\Dto\ResponseDto
     */
    public function isValidPackage(Package $package): ResponseDto;
}
