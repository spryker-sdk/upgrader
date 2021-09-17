<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis;

use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\HttpResponse;
use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\Collection\UpgradeAnalysisModuleCollection;
use Upgrader\Business\Exception\UpgraderException;

class UpgradeAnalysisResponse extends HttpResponse
{
    public const MODULES_KEY = 'modules';

    /**
     * @var \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\Collection\UpgradeAnalysisModuleCollection
     */
    protected $moduleCollection;

    /**
     * @return \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\Collection\UpgradeAnalysisModuleCollection
     */
    public function getModuleCollection(): UpgradeAnalysisModuleCollection
    {
        if (!$this->moduleCollection instanceof UpgradeAnalysisModuleCollection) {
            $moduleList = [];
            foreach ($this->getModulesArray() as $moduleData) {
                $moduleList[] = new UpgradeAnalysisModule($moduleData);
            }

            $this->moduleCollection = new UpgradeAnalysisModuleCollection($moduleList);
        }

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
