<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Adapter;

use PackageManager\Application\Service\PackageManagerServiceInterface;
use ReleaseAppClient\Domain\Client\ReleaseAppClientInterface;
use ReleaseAppClient\Domain\Dto\ReleaseAppClientRequestDto;
use ReleaseAppClient\Domain\Dto\ReleaseAppClientResponseDto;
use Upgrade\Domain\Adapter\ReleaseAppClientAdapterInterface;

class ReleaseAppClientAdapter implements ReleaseAppClientAdapterInterface
{
    /**
     * @var \ReleaseAppClient\Domain\Client\ReleaseAppClientInterface
     */
    protected ReleaseAppClientInterface $releaseAppClient;

    /**
     * @var \PackageManager\Application\Service\PackageManagerServiceInterface
     */
    protected $packageManager;

    /**
     * @param \ReleaseAppClient\Domain\Client\ReleaseAppClientInterface $dataProvider
     * @param \PackageManager\Application\Service\PackageManagerServiceInterface $packageManager
     */
    public function __construct(ReleaseAppClientInterface $dataProvider, PackageManagerServiceInterface $packageManager)
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
