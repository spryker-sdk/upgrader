<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Adapter;

use PackageManager\Application\Service\PackageManagerServiceInterface;
use ReleaseApp\Domain\Client\Request\UpgradeAnalysisRequest;
use ReleaseApp\Infrastructure\Service\ReleaseAppServiceInterface;
use ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse;
use Upgrade\Application\Provider\ReleaseAppClientProviderInterface;

class ReleaseAppClientProvider implements ReleaseAppClientProviderInterface
{
    /**
     * @var \ReleaseApp\Application\Service\ReleaseAppServiceInterface
     */
    protected ReleaseAppServiceInterface $releaseApp;

    /**
     * @var \PackageManager\Application\Service\PackageManagerServiceInterface
     */
    protected $packageManager;

    /**
     * @param \ReleaseApp\Application\Service\ReleaseAppServiceInterface $dataProvider
     * @param \PackageManager\Application\Service\PackageManagerServiceInterface $packageManager
     */
    public function __construct(ReleaseAppServiceInterface $dataProvider, PackageManagerServiceInterface $packageManager)
    {
        $this->releaseApp = $dataProvider;
        $this->packageManager = $packageManager;
    }

    /**
     * @return \ReleaseApp\Infrastructure\Shared\Dto\ReleaseAppResponse
     */
    public function getNotInstalledReleaseGroupList(): ReleaseAppResponse
    {
        $request = $this->createDataProviderRequest();

        return $this->releaseApp->getNotInstalledReleaseGroupList($request);
    }


    /**
     * @return UpgradeAnalysisRequest
     */
    protected function createDataProviderRequest(): UpgradeAnalysisRequest
    {
        $projectName = $this->packageManager->getProjectName();
        $composerJson = $this->packageManager->getComposerJsonFile();
        $composerLock = $this->packageManager->getComposerLockFile();

        return new UpgradeAnalysisRequest($projectName, $composerJson, $composerLock);
    }
}
