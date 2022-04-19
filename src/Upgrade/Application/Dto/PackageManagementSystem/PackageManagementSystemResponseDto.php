<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Dto\PackageManagementSystem;

use Upgrade\Application\Dto\PackageManagementSystem\Collection\ReleaseGroupDtoCollection;

class PackageManagementSystemResponseDto
{
    /**
     * @var \Upgrade\Application\Dto\PackageManagementSystem\Collection\ReleaseGroupDtoCollection
     */
    protected $releaseGroupCollection;

    /**
     * @param \Upgrade\Application\Dto\PackageManagementSystem\Collection\ReleaseGroupDtoCollection $releaseGroupCollection
     */
    public function __construct(ReleaseGroupDtoCollection $releaseGroupCollection)
    {
        $this->releaseGroupCollection = $releaseGroupCollection;
    }

    /**
     * @return \Upgrade\Application\Dto\PackageManagementSystem\Collection\ReleaseGroupDtoCollection
     */
    public function getReleaseGroupCollection(): ReleaseGroupDtoCollection
    {
        return $this->releaseGroupCollection;
    }
}
