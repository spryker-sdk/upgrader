<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Validator\Package;

use Upgrader\Business\PackageManager\Transfer\PackageTransfer;

interface PackageValidatorInterface
{
    /**
     * @param \Upgrader\Business\PackageManager\Transfer\PackageTransfer $package
     *
     * @throws \Upgrader\Business\Exception\UpgraderException
     *
     * @return void
     */
    public function validate(PackageTransfer $package): void;
}
