<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Validator\Package;

use Upgrade\Application\Dto\PackageManager\PackageDto;

interface PackageValidatorInterface
{
    /**
     * @param \Upgrade\Application\Dto\PackageManager\PackageDto $package
     *
     * @throws \Upgrade\Infrastructure\Exception\UpgraderException
     *
     * @return void
     */
    public function validate(PackageDto $package): void;
}
