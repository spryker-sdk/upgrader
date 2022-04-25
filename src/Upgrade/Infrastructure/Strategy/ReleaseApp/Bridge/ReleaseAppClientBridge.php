<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy\ReleaseApp\Bridge;

use ReleaseAppClient\Domain\Dto\ReleaseAppClientRequestDto;
use ReleaseAppClient\Domain\Dto\ReleaseAppClientResponseDto;
use ReleaseAppClient\Domain\Client\ReleaseAppClientInterface;
use PackageManager\Application\Service\PackageManagerInterface;

class ReleaseAppClientBridge implements ReleaseAppClientBridgeInterface
{
    /**
     * @var ReleaseAppClientInterface
     */
    protected ReleaseAppClientInterface $releaseAppClient;

    /**
     * @var \PackageManager\Application\Service\PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @param ReleaseAppClientInterface $dataProvider
     * @param \PackageManager\Application\Service\PackageManagerInterface $packageManager
     */
    public function __construct(ReleaseAppClientInterface $dataProvider, PackageManagerInterface $packageManager)
    {
        $this->releaseAppClient = $dataProvider;
        $this->packageManager = $packageManager;
    }

    /**
     * @return \ReleaseAppClient\Domain\Dto\ReleaseAppClientResponseDto
     */
    public function getNotInstalledReleaseGroupList(): ReleaseAppClientResponseDto
    {
        $request = $this->createDataProviderRequest();

        return $this->releaseAppClient->getNotInstalledReleaseGroupList($request);
    }

    /**
     * @return \ReleaseAppClient\Domain\Dto\ReleaseAppClientRequestDto
     */
    protected function createDataProviderRequest(): ReleaseAppClientRequestDto
    {
        $projectName = $this->packageManager->getProjectName();
        $composerJson = $this->packageManager->getComposerJsonFile();
        $composerLock = $this->packageManager->getComposerLockFile();

        return new ReleaseAppClientRequestDto($projectName, $composerJson, $composerLock);
    }
}
