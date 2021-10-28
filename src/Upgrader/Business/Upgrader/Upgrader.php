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
        $checkResponse = $this->vcs->checkTargetBranchExists();
        $responses->add($checkResponse);
        if (!$checkResponse->isSuccess()) {
            return $responses;
        }
        $checkResponse = $this->vcs->checkUncommittedChanges();
        $responses->add($checkResponse);
        if (!$checkResponse->isSuccess()) {
            return $responses;
        }
        $dataProviderResponse = $this->dataProviderManager->getNotInstalledReleaseGroupList();
        $packageManagerResponses = $this->releaseGroupManager->requireCollection(
            $dataProviderResponse->getReleaseGroupCollection()
        );
        $responses->addCollection($packageManagerResponses);
        if (!$packageManagerResponses->hasSuccessfulResponse()) {
            return $responses;
        }
        $vcsResponses = $this->vcs->save($packageManagerResponses->getSuccessfulReleaseGroups());
        $responses->addCollection($vcsResponses);
        $responses->add($this->vcs->checkout());
        if ($vcsResponses->hasResponseWithError()) {
            $rollbackResponses = $this->vcs->rollback();
            $responses->addCollection($rollbackResponses);
        }

        return $responses;
    }
}
