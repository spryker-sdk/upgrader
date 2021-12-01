<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Strategy;

use Upgrader\Business\Upgrader\Bridge\PackageManagementSystemBridgeInterface;
use Upgrader\Business\Upgrader\Bridge\ReleaseGroupTransferBridgeInterface;
use Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection;

class ReleaseGroupStrategy implements UpgradeStrategyInterface
{
    /**
     * @var \Upgrader\Business\Upgrader\Bridge\ReleaseGroupTransferBridgeInterface
     */
    protected $releaseGroupManager;

    /**
     * @var \Upgrader\Business\Upgrader\Bridge\PackageManagementSystemBridgeInterface
     */
    protected $dataProviderManager;

    /**
     * @param \Upgrader\Business\Upgrader\Bridge\ReleaseGroupTransferBridgeInterface $releaseGroupManager
     * @param \Upgrader\Business\Upgrader\Bridge\PackageManagementSystemBridgeInterface $dataProviderManager
     */
    public function __construct(
        ReleaseGroupTransferBridgeInterface $releaseGroupManager,
        PackageManagementSystemBridgeInterface $dataProviderManager
    ) {
        $this->releaseGroupManager = $releaseGroupManager;
        $this->dataProviderManager = $dataProviderManager;
    }

    /**
     * @return \Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection
     */
    public function upgrade(): UpgraderResponseCollection
    {
        $responses = new UpgraderResponseCollection();

        $dataProviderResponse = $this->dataProviderManager->getNotInstalledReleaseGroupList();
        $requireResponses = $this->releaseGroupManager->requireCollection(
            $dataProviderResponse->getReleaseGroupCollection(),
        );
        $responses->addCollection($requireResponses);

        return $responses;
    }
}
