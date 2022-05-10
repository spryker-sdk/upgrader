<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Domain\Entities;

use ReleaseApp\Domain\Client\Response\Response;
use ReleaseApp\Domain\Entities\Collection\UpgradeAnalysisModuleCollection;
use Upgrade\Application\Exception\UpgraderException;

class UpgradeAnalysis extends Response
{
    /**
     * @var string
     */
    protected const MODULES_KEY = 'modules';

    /**
     * @var \ReleaseApp\Domain\Entities\Collection\UpgradeAnalysisModuleCollection|null
     */
    protected $moduleCollection;

    /**
     * @return \ReleaseApp\Domain\Entities\Collection\UpgradeAnalysisModuleCollection
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
     * @return array
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
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
