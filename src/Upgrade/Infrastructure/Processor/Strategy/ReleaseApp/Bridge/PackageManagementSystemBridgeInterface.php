<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Bridge;

use Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemResponseDto;

interface PackageManagementSystemBridgeInterface
{
    /**
     * @return \Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemResponseDto
     */
    public function getNotInstalledReleaseGroupList(): PackageManagementSystemResponseDto;
}
