<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Response;

use Upgrade\Infrastructure\Exception\UpgraderException;
use Upgrade\Infrastructure\ReleaseAppClient\Http\HttpResponse;
use Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Response\Collection\HttpUpgradeAnalysisModuleCollection;

class HttpUpgradeAnalysisResponse extends HttpResponse
{
    /**
     * @var string
     */
    protected const MODULES_KEY = 'modules';

    /**
     * @var \Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Response\Collection\HttpUpgradeAnalysisModuleCollection|null
     */
    protected $moduleCollection;

    /**
     * @return \Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Response\Collection\HttpUpgradeAnalysisModuleCollection
     */
    public function getModuleCollection(): HttpUpgradeAnalysisModuleCollection
    {
        if ($this->moduleCollection) {
            return $this->moduleCollection;
        }

        $moduleList = [];
        foreach ($this->getModulesArray() as $moduleData) {
            $moduleList[] = new HttpUpgradeAnalysisModule($moduleData);
        }
        $this->moduleCollection = new HttpUpgradeAnalysisModuleCollection($moduleList);

        return $this->moduleCollection;
    }

    /**
     * @throws \Upgrade\Infrastructure\Exception\UpgraderException
     *
     * @return array
     */
    protected function getModulesArray(): array
    {
        $bodyArray = $this->getBody();

        if (!$bodyArray) {
            throw new UpgraderException('Response body not found');
        }

        $modulesArray = $bodyArray[static::MODULES_KEY];

        if (!$modulesArray) {
            throw new UpgraderException('Key module not found');
        }

        return $modulesArray;
    }
}
