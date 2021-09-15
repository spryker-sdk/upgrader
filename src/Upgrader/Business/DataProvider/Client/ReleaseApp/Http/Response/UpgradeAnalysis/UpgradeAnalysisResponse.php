<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis;

use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\HttpResponse;
use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\Collection\UpgradeAnalysisModuleCollection;

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
            $bodyArray = $this->getBodyArray();

            $moduleList = [];
            foreach ($bodyArray[self::MODULES_KEY] as $moduleData) {
                $moduleList[] = new UpgradeAnalysisModule($moduleData);
            }

            $this->moduleCollection = new UpgradeAnalysisModuleCollection($moduleList);
        }

        return $this->moduleCollection;
    }
}
