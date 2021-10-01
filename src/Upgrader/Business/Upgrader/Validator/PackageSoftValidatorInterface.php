<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Validator;

use Upgrader\Business\PackageManager\Response\PackageManagerResponse;
use Upgrader\Business\PackageManager\Transfer\PackageTransfer;

interface PackageSoftValidatorInterface
{
    /**
     * @param \Upgrader\Business\PackageManager\Transfer\PackageTransfer $package
     *
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    public function isValidPackage(PackageTransfer $package): PackageManagerResponse;
}
