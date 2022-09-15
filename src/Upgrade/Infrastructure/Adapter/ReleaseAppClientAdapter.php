<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Adapter;

use ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest;
use ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;
use Upgrade\Application\Adapter\PackageManagerAdapterInterface;
use Upgrade\Application\Adapter\ReleaseAppClientAdapterInterface;

class ReleaseAppClientAdapter implements ReleaseAppClientAdapterInterface
{
    /**
     * @var \ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface
     */
    protected ReleaseAppServiceInterface $releaseApp;

    /**
     * @var \Upgrade\Application\Adapter\PackageManagerAdapterInterface
     */
    protected PackageManagerAdapterInterface $packageManager;

    /**
     * @param \ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface $dataProvider
     * @param \Upgrade\Application\Adapter\PackageManagerAdapterInterface $packageManager
     */
    public function __construct(ReleaseAppServiceInterface $dataProvider, PackageManagerAdapterInterface $packageManager)
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
