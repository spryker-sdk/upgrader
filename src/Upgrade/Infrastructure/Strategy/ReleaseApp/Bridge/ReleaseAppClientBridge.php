<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Bridge;

use Upgrade\Application\Dto\ReleaseAppClient\ReleaseAppClientRequestDto;
use Upgrade\Application\Dto\ReleaseAppClient\ReleaseAppClientResponseDto;
use Upgrade\Infrastructure\ReleaseAppClient\ReleaseAppClientInterface;
use Upgrade\Infrastructure\PackageManagementSystem\PackageManagementSystemInterface;
use Upgrade\Infrastructure\PackageManager\PackageManagerInterface;

class ReleaseAppClientBridge implements ReleaseAppClientBridgeInterface
{
    /**
     * @var ReleaseAppClientInterface
     */
    protected $releaseAppClient;

    /**
     * @var \Upgrade\Infrastructure\PackageManager\PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @param ReleaseAppClientInterface $dataProvider
     * @param \Upgrade\Infrastructure\PackageManager\PackageManagerInterface $packageManager
     */
    public function __construct(ReleaseAppClientInterface $dataProvider, PackageManagerInterface $packageManager)
    {
        $this->releaseAppClient = $dataProvider;
        $this->packageManager = $packageManager;
    }

    /**
     * @return \Upgrade\Application\Dto\ReleaseAppClient\ReleaseAppClientResponseDto
     */
    public function getNotInstalledReleaseGroupList(): ReleaseAppClientResponseDto
    {
        $request = $this->createDataProviderRequest();

        return $this->releaseAppClient->getNotInstalledReleaseGroupList($request);
    }

    /**
     * @return \Upgrade\Application\Dto\ReleaseAppClient\ReleaseAppClientRequestDto
     */
    protected function createDataProviderRequest(): ReleaseAppClientRequestDto
    {
        $projectName = $this->packageManager->getProjectName();
        $composerJson = $this->packageManager->getComposerJsonFile();
        $composerLock = $this->packageManager->getComposerLockFile();

        return new ReleaseAppClientRequestDto($projectName, $composerJson, $composerLock);
    }
}
