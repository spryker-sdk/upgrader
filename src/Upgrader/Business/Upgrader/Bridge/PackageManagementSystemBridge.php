<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Bridge;

use Upgrader\Business\PackageManagementSystem\PackageManagementSystemInterface;
use Upgrader\Business\PackageManagementSystem\Request\PackageManagementSystemRequest;
use Upgrader\Business\PackageManagementSystem\Response\PackageManagementSystemResponse;
use Upgrader\Business\PackageManager\PackageManagerInterface;

class PackageManagementSystemBridge implements PackageManagementSystemBridgeInterface
{
    /**
     * @var \Upgrader\Business\PackageManagementSystem\PackageManagementSystemInterface
     */
    protected $dataProvider;

    /**
     * @var \Upgrader\Business\PackageManager\PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @param \Upgrader\Business\PackageManagementSystem\PackageManagementSystemInterface $dataProvider
     * @param \Upgrader\Business\PackageManager\PackageManagerInterface $packageManager
     */
    public function __construct(PackageManagementSystemInterface $dataProvider, PackageManagerInterface $packageManager)
    {
        $this->dataProvider = $dataProvider;
        $this->packageManager = $packageManager;
    }

    /**
     * @return \Upgrader\Business\PackageManagementSystem\Request\PackageManagementSystemRequest
     */
    protected function createDataProviderRequest(): PackageManagementSystemRequest
    {
        $projectName = $this->packageManager->getProjectName();
        $composerJson = $this->packageManager->getComposerJsonFile();
        $composerLock = $this->packageManager->getComposerLockFile();

        return new PackageManagementSystemRequest($projectName, $composerJson, $composerLock);
    }

    /**
     * @return \Upgrader\Business\PackageManagementSystem\Response\PackageManagementSystemResponse
     */
    public function getNotInstalledReleaseGroupList(): PackageManagementSystemResponse
    {
        $request = $this->createDataProviderRequest();

        return $this->dataProvider->getNotInstalledReleaseGroupList($request);
    }
}
