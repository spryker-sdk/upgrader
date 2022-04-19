<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManagementSystem;

use Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemRequestDto;
use Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemResponseDto;

interface PackageManagementSystemInterface
{
    /**
     * @param \Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemRequestDto $request
     *
     * @return \Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemResponseDto
     */
    public function getNotInstalledReleaseGroupList(PackageManagementSystemRequestDto $request): PackageManagementSystemResponseDto;
}
