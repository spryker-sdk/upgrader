<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Bridge;

use Upgrader\Business\Command\Response\Collection\CommandResponseCollection;
use Upgrader\Business\Command\Response\CommandResponse;
use Upgrader\Business\PackageManagementSystem\Transfer\Collection\ReleaseGroupTransferCollection;
use Upgrader\Business\PackageManagementSystem\Transfer\ReleaseGroupTransfer;

interface ReleaseGroupTransferBridgeInterface
{
    /**
     * @param \Upgrader\Business\PackageManagementSystem\Transfer\Collection\ReleaseGroupTransferCollection $releaseGroupCollection
     *
     * @return \Upgrader\Business\Command\Response\Collection\CommandResponseCollection
     */
    public function requireCollection(ReleaseGroupTransferCollection $releaseGroupCollection): CommandResponseCollection;

    /**
     * @param \Upgrader\Business\PackageManagementSystem\Transfer\ReleaseGroupTransfer $releaseGroup
     *
     * @return \Upgrader\Business\Command\Response\CommandResponse
     */
    public function require(ReleaseGroupTransfer $releaseGroup): CommandResponse;
}
