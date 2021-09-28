<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response;

use Upgrader\Business\Exception\UpgraderException;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\HttpResponse;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\Collection\UpgradeAnalysisModuleCollection;

class UpgradeAnalysisResponse extends HttpResponse
{
    protected const MODULES_KEY = 'modules';

    /**
     * @var \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\Collection\UpgradeAnalysisModuleCollection|null
     */
    protected $moduleCollection;

    /**
     * @return \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\Collection\UpgradeAnalysisModuleCollection
     */
    public function getModuleCollection(): UpgradeAnalysisModuleCollection
    {
        if ($this->moduleCollection) {
            return $this->moduleCollection;
        }

        $moduleList = [];
        foreach ($this->getModulesArray() as $moduleData) {
            $moduleList[] = new UpgradeAnalysisModule($moduleData);
        }
        $this->moduleCollection = new UpgradeAnalysisModuleCollection($moduleList);

        return $this->moduleCollection;
    }

    /**
     * @throws \Upgrader\Business\Exception\UpgraderException
     *
     * @return array
     */
    protected function getModulesArray(): array
    {
        $bodyArray = $this->getBodyArray();

        if (!$bodyArray) {
            throw new UpgraderException('Response body not found');
        }

        $modulesArray = $bodyArray[self::MODULES_KEY];

        if (!$modulesArray) {
            throw new UpgraderException('Key module not found');
        }

        return $modulesArray;
    }
}
