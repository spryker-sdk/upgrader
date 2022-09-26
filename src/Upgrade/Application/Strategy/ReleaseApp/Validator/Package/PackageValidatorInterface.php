<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Validator\Package;

use Upgrade\Domain\Entity\Package;

interface PackageValidatorInterface
{
    /**
     * @param \Upgrade\Domain\Entity\Package $package
     *
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
     * @return void
     */
    public function validate(Package $package): void;
}
