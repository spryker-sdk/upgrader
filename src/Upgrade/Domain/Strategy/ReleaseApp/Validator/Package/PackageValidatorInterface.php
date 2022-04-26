<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\ReleaseApp\Validator\Package;

use PackageManager\Domain\Dto\PackageDto;

interface PackageValidatorInterface
{
    /**
     * @param \PackageManager\Domain\Dto\PackageDto $package
     *
     * @throws \Upgrade\Infrastructure\Exception\UpgraderException
     *
     * @return void
     */
    public function validate(PackageDto $package): void;
}
