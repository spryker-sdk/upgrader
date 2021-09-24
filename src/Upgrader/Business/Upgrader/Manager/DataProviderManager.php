<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Manager;

use Upgrader\Business\DataProvider\DataProviderInterface;
use Upgrader\Business\DataProvider\Request\DataProviderRequest;
use Upgrader\Business\DataProvider\Response\DataProviderResponse;
use Upgrader\Business\PackageManager\PackageManagerInterface;

class DataProviderManager implements DataProviderRequestManagerInterface
{
    /**
     * @var \Upgrader\Business\DataProvider\DataProviderInterface
     */
    protected $dataProvider;

    /**
     * @var \Upgrader\Business\PackageManager\PackageManagerInterface
     */
    protected $packageManager;

    /**
     * @param \Upgrader\Business\DataProvider\DataProviderInterface $dataProvider
     * @param \Upgrader\Business\PackageManager\PackageManagerInterface $packageManager
     */
    public function __construct(DataProviderInterface $dataProvider, PackageManagerInterface $packageManager)
    {
        $this->dataProvider = $dataProvider;
        $this->packageManager = $packageManager;
    }

    /**
     * @return \Upgrader\Business\DataProvider\Request\DataProviderRequest
     */
    protected function createDataProviderRequest(): DataProviderRequest
    {
        $projectName = $this->packageManager->getProjectName();
        $composerJson = $this->packageManager->getComposerJsonFile();
        $composerLock = $this->packageManager->getComposerLockFile();

        return new DataProviderRequest($projectName, $composerJson, $composerLock);
    }

    /**
     * @return \Upgrader\Business\DataProvider\Response\DataProviderResponse
     */
    public function getNotInstalledReleaseGroupList(): DataProviderResponse
    {
        $request = $this->createDataProviderRequest();

        return $this->dataProvider->getNotInstalledReleaseGroupList($request);
    }
}
