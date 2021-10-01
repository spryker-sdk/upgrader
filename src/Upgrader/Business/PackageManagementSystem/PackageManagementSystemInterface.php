<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem;

use Upgrader\Business\PackageManagementSystem\Request\PackageManagementSystemRequestInterface;
use Upgrader\Business\PackageManagementSystem\Response\PackageManagementSystemResponse;

interface PackageManagementSystemInterface
{
    /**
     * @param \Upgrader\Business\PackageManagementSystem\Request\PackageManagementSystemRequestInterface $request
     *
     * @return \Upgrader\Business\PackageManagementSystem\Response\PackageManagementSystemResponse
     */
    public function getNotInstalledReleaseGroupList(PackageManagementSystemRequestInterface $request): PackageManagementSystemResponse;
}
