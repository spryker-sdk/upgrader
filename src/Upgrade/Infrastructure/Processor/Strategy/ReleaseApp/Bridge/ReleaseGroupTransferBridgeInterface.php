<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Bridge;

use Upgrade\Application\Dto\PackageManagementSystem\Collection\ReleaseGroupDtoCollection;
use Upgrade\Application\Dto\PackageManagementSystem\ReleaseGroupDto;
use Upgrade\Application\Dto\PackageManager\Collection\PackageManagerResponseDtoCollection;
use Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto;

interface ReleaseGroupTransferBridgeInterface
{
    /**
     * @param \Upgrade\Application\Dto\PackageManagementSystem\Collection\ReleaseGroupDtoCollection $releaseGroupCollection
     *
     * @return \Upgrade\Application\Dto\PackageManager\Collection\PackageManagerResponseDtoCollection
     */
    public function requireCollection(ReleaseGroupDtoCollection $releaseGroupCollection): PackageManagerResponseDtoCollection;

    /**
     * @param \Upgrade\Application\Dto\PackageManagementSystem\ReleaseGroupDto $releaseGroup
     *
     * @return \Upgrade\Application\Dto\PackageManager\PackageManagerResponseDtoDto
     */
    public function require(ReleaseGroupDto $releaseGroup): PackageManagerResponseDtoDto;
}
