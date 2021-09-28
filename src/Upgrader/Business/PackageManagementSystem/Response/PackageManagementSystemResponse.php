<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem\Response;

use Upgrader\Business\PackageManagementSystem\Transfer\Collection\ReleaseGroupTransferCollection;

class PackageManagementSystemResponse
{
    /**
     * @var \Upgrader\Business\PackageManagementSystem\Transfer\Collection\ReleaseGroupTransferCollection
     */
    protected $releaseGroupCollection;

    /**
     * @param \Upgrader\Business\PackageManagementSystem\Transfer\Collection\ReleaseGroupTransferCollection $releaseGroupCollection
     */
    public function __construct(ReleaseGroupTransferCollection $releaseGroupCollection)
    {
        $this->releaseGroupCollection = $releaseGroupCollection;
    }

    /**
     * @return \Upgrader\Business\PackageManagementSystem\Transfer\Collection\ReleaseGroupTransferCollection
     */
    public function getReleaseGroupCollection(): ReleaseGroupTransferCollection
    {
        return $this->releaseGroupCollection;
    }
}
