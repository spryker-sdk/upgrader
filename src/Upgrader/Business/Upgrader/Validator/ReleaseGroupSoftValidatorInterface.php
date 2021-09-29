<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Validator;

use Upgrader\Business\PackageManagementSystem\Transfer\ReleaseGroupTransfer;
use Upgrader\Business\PackageManager\Response\PackageManagerResponse;

interface ReleaseGroupSoftValidatorInterface
{
    /**
     * @param \Upgrader\Business\PackageManagementSystem\Transfer\ReleaseGroupTransfer $releaseGroup
     *
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    public function isValidReleaseGroup(ReleaseGroupTransfer $releaseGroup): PackageManagerResponse;
}
