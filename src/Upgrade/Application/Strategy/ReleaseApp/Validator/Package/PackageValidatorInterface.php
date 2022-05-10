<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\ReleaseApp\Validator\Package;

use Upgrade\Domain\Entity\Package;

interface PackageValidatorInterface
{
    /**
     * @param \Upgrade\Domain\Entity\Package $package
     *
     * @return void
     *@throws \Upgrade\Application\Exception\UpgraderException
     *
     */
    public function validate(Package $package): void;
}
