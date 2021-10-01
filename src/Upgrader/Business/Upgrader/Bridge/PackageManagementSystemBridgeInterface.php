<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Bridge;

use Upgrader\Business\PackageManagementSystem\Response\PackageManagementSystemResponse;

interface PackageManagementSystemBridgeInterface
{
    /**
     * @return \Upgrader\Business\PackageManagementSystem\Response\PackageManagementSystemResponse
     */
    public function getNotInstalledReleaseGroupList(): PackageManagementSystemResponse;
}
