<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Bridge;

use Upgrader\Business\PackageManagementSystem\Transfer\Collection\ReleaseGroupTransferCollection;
use Upgrader\Business\PackageManagementSystem\Transfer\ReleaseGroupTransfer;
use Upgrader\Business\PackageManager\Response\Collection\PackageManagerResponseCollection;
use Upgrader\Business\PackageManager\Response\PackageManagerResponse;

interface ReleaseGroupTransferBridgeInterface
{
    /**
     * @param \Upgrader\Business\PackageManagementSystem\Transfer\Collection\ReleaseGroupTransferCollection $releaseGroupCollection
     *
     * @return \Upgrader\Business\PackageManager\Response\Collection\PackageManagerResponseCollection
     */
    public function requireCollection(ReleaseGroupTransferCollection $releaseGroupCollection): PackageManagerResponseCollection;

    /**
     * @param \Upgrader\Business\PackageManagementSystem\Transfer\ReleaseGroupTransfer $releaseGroup
     *
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    public function require(ReleaseGroupTransfer $releaseGroup): PackageManagerResponse;
}
