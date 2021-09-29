<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader;

use Upgrader\Business\Upgrader\Bridge\PackageManagementSystemBridge;
use Upgrader\Business\Upgrader\Bridge\ReleaseGroupTransferBridgeInterface;
use Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection;
use Upgrader\Business\VersionControlSystem\VcsInterface;

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
     * @var \Upgrader\Business\VersionControlSystem\VcsInterface
     */
    protected $vcs;

    /**
     * @param \Upgrader\Business\Upgrader\Bridge\ReleaseGroupTransferBridgeInterface $releaseGroupManager
     * @param \Upgrader\Business\Upgrader\Bridge\PackageManagementSystemBridge $dataProviderManager
     * @param \Upgrader\Business\VersionControlSystem\VcsInterface $vcs
     */
    public function __construct(
        ReleaseGroupTransferBridgeInterface $releaseGroupManager,
        PackageManagementSystemBridge $dataProviderManager,
        VcsInterface $vcs
    ) {
        $this->releaseGroupManager = $releaseGroupManager;
        $this->dataProviderManager = $dataProviderManager;
        $this->vcs = $vcs;
    }

    /**
     * @return \Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection
     */
    public function upgrade(): UpgraderResponseCollection
    {
        $responses = new UpgraderResponseCollection();
        $checkResponse = $this->vcs->check();
        $responses->add($checkResponse);
        if (!$checkResponse->isSuccess()) {
            return $responses;
        }
        $dataProviderResponse = $this->dataProviderManager->getNotInstalledReleaseGroupList();
        $packageManagerResponses = $this->releaseGroupManager->requireCollection(
            $dataProviderResponse->getReleaseGroupCollection()
        );
        foreach ($packageManagerResponses->toArray() as $item) {
            $responses->add($item);
        }
        $vcsResponses = $this->vcs->save();
        foreach ($vcsResponses->toArray() as $item) {
            $responses->add($item);
        }

        return $responses;
    }
}
