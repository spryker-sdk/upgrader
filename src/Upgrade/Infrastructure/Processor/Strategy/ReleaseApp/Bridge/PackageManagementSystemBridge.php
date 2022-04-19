<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\ReleaseApp\Bridge;

use Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemRequestDto;
use Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemResponseDto;
use Upgrade\Infrastructure\PackageManagementSystem\PackageManagementSystemInterface;
use Upgrade\Infrastructure\PackageManager\PackageManagerInterface;

class PackageManagementSystemBridge implements PackageManagementSystemBridgeInterface
{
    /**
     * @var \Upgrade\Infrastructure\PackageManagementSystem\PackageManagementSystemInterface
     */
    protected $dataProvider;

    /**
     * @var \Upgrade\Infrastructure\PackageManager\PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @param \Upgrade\Infrastructure\PackageManagementSystem\PackageManagementSystemInterface $dataProvider
     * @param \Upgrade\Infrastructure\PackageManager\PackageManagerInterface $packageManager
     */
    public function __construct(PackageManagementSystemInterface $dataProvider, PackageManagerInterface $packageManager)
    {
        $this->dataProvider = $dataProvider;
        $this->packageManager = $packageManager;
    }

    /**
     * @return \Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemRequestDto
     */
    protected function createDataProviderRequest(): PackageManagementSystemRequestDto
    {
        $projectName = $this->packageManager->getProjectName();
        $composerJson = $this->packageManager->getComposerJsonFile();
        $composerLock = $this->packageManager->getComposerLockFile();

        return new PackageManagementSystemRequestDto($projectName, $composerJson, $composerLock);
    }

    /**
     * @return \Upgrade\Application\Dto\PackageManagementSystem\PackageManagementSystemResponseDto
     */
    public function getNotInstalledReleaseGroupList(): PackageManagementSystemResponseDto
    {
        $request = $this->createDataProviderRequest();

        return $this->dataProvider->getNotInstalledReleaseGroupList($request);
    }
}
