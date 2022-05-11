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
    protected ?UpgradeAnalysisModuleCollection $moduleCollection;

    /**
     * @return \ReleaseApp\Domain\Entities\Collection\UpgradeAnalysisModuleCollection
     */
    public function getModuleCollection(): UpgradeAnalysisModuleCollection
    {
        if ($this->moduleCollection) {
            return $this->moduleCollection;
        }

        $moduleList = [];
        foreach ($this->getModules() as $moduleData) {
            $moduleList[] = new UpgradeAnalysisModule($moduleData);
        }
        $this->moduleCollection = new UpgradeAnalysisModuleCollection($moduleList);

        return $this->moduleCollection;
    }

    /**
     * @throws \Upgrade\Application\Exception\UpgraderException
     *
     * @return array
     */
    protected function getModules(): array
    {
        if (!$this->body) {
            throw new UpgraderException('Response body not found');
        }

        if (!array_key_exists(static::MODULES_KEY, $this->body)) {
            throw new UpgraderException('Key modules not found');
        }

        return $this->body[static::MODULES_KEY];
    }
}
