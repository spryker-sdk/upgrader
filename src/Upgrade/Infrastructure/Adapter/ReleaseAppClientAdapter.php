<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Adapter;

use PackageManager\Application\Service\PackageManagerServiceInterface;
use ReleaseApp\Application\Service\ReleaseAppServiceInterface;
use ReleaseApp\Infrastructure\Presentation\Entity\ReleaseAppClientRequestDto;
use ReleaseApp\Infrastructure\Presentation\Entity\ReleaseAppResponse;
use Upgrade\Domain\Adapter\ReleaseAppClientAdapterInterface;

class ReleaseAppClientAdapter implements ReleaseAppClientAdapterInterface
{
    /**
     * @var \ReleaseApp\Application\Service\ReleaseAppServiceInterface
     */
    protected ReleaseAppServiceInterface $releaseAppClient;

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
        $this->releaseAppClient = $dataProvider;
        $this->packageManager = $packageManager;
    }

    /**
     * @return \ReleaseApp\Infrastructure\Presentation\Entity\ReleaseAppResponse
     */
    public function getNotInstalledReleaseGroupList(): ReleaseAppResponse
    {
        $request = $this->createDataProviderRequest();

        return $this->releaseAppClient->getNotInstalledReleaseGroupList($request);
    }

    /**
     * @return \ReleaseApp\Infrastructure\Presentation\Entity\ReleaseAppClientRequestDto
     */
    protected function createDataProviderRequest(): ReleaseAppClientRequestDto
    {
        $projectName = $this->packageManager->getProjectName();
        $composerJson = $this->packageManager->getComposerJsonFile();
        $composerLock = $this->packageManager->getComposerLockFile();

        return new ReleaseAppClientRequestDto($projectName, $composerJson, $composerLock);
    }
}
