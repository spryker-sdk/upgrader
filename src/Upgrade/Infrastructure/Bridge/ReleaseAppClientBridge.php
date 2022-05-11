<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Bridge;

use ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest;
use ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;
use Upgrade\Application\Bridge\PackageManagerBridgeInterface;
use Upgrade\Application\Bridge\ReleaseAppClientBridgeInterface;

class ReleaseAppClientBridge implements ReleaseAppClientBridgeInterface
{
    /**
     * @var \ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface
     */
    protected ReleaseAppServiceInterface $releaseApp;

    /**
     * @var \Upgrade\Application\Bridge\PackageManagerBridgeInterface
     */
    protected PackageManagerBridgeInterface $packageManager;

    /**
     * @param \ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface $dataProvider
     * @param \Upgrade\Application\Bridge\PackageManagerBridgeInterface $packageManager
     */
    public function __construct(ReleaseAppServiceInterface $dataProvider, PackageManagerBridgeInterface $packageManager)
    {
        $this->releaseApp = $dataProvider;
        $this->packageManager = $packageManager;
    }

    /**
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse
     */
    public function getNewReleaseGroups(): ReleaseAppResponse
    {
        $upgradeAnalysisRequest = $this->createDataProviderRequest();

        return $this->releaseApp->getNewReleaseGroups($upgradeAnalysisRequest);
    }

    /**
     * @return \ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest
     */
    protected function createDataProviderRequest(): UpgradeAnalysisRequest
    {
        $projectName = $this->packageManager->getProjectName();
        $composerJson = $this->packageManager->getComposerJsonFile();
        $composerLock = $this->packageManager->getComposerLockFile();

        return new UpgradeAnalysisRequest($projectName, $composerJson, $composerLock);
    }
}
