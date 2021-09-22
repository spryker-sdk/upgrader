<?php

namespace Upgrader\Business\Upgrader\Bridge;

use Upgrader\Business\DataProvider\Request\DataProviderRequest;

class DataProviderBridge
{

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

}
