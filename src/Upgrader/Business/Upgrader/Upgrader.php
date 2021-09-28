<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader;

use Upgrader\Business\Command\Response\Collection\CommandResponseCollection;
use Upgrader\Business\Upgrader\Bridge\PackageManagementSystemBridge;
use Upgrader\Business\Upgrader\Bridge\ReleaseGroupTransferBridgeInterface;

class Upgrader implements UpgraderInterface
{
    /**
     * @var \Upgrader\Business\Upgrader\Bridge\ReleaseGroupTransferBridgeInterface
     */
    protected $releaseGroupManager;

    /**
     * @var \Upgrader\Business\Upgrader\Bridge\PackageManagementSystemBridge
     */
    protected $dataProviderManager;

    /**
     * @param Bridge\ReleaseGroupTransferBridgeInterface $releaseGroupManager
     * @param Bridge\PackageManagementSystemBridge $dataProviderManager
     */
    public function __construct(
        ReleaseGroupTransferBridgeInterface $releaseGroupManager,
        PackageManagementSystemBridge $dataProviderManager
    ) {
        $this->releaseGroupManager = $releaseGroupManager;
        $this->dataProviderManager = $dataProviderManager;
    }

    /**
     * @return \Upgrader\Business\Command\Response\Collection\CommandResponseCollection
     */
    public function upgrade(): CommandResponseCollection
    {
        $dataProviderResponse = $this->dataProviderManager->getNotInstalledReleaseGroupList();

        return $this->releaseGroupManager->requireCollection($dataProviderResponse->getReleaseGroupCollection());
    }
}
