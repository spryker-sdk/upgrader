<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Validator;

use Upgrader\Business\Command\Response\CommandResponse;
use Upgrader\Business\PackageManagementSystem\Transfer\ReleaseGroupTransfer;

interface ReleaseGroupSoftValidatorInterface
{
    /**
     * @param \Upgrader\Business\PackageManagementSystem\Transfer\ReleaseGroupTransfer $releaseGroup
     *
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    public function isValidReleaseGroup(ReleaseGroupTransfer $releaseGroup): CommandResponse;
}
