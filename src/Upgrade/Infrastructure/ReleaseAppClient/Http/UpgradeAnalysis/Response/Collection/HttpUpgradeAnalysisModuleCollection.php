<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Response\Collection;

use Upgrade\Application\Dto\Collection\UpgraderCollection;
use Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Response\HttpUpgradeAnalysisModule;

/**
 * @method \Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Response\HttpUpgradeAnalysisModule[]|\ArrayIterator|\Traversable getIterator()
 */
class HttpUpgradeAnalysisModuleCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return HttpUpgradeAnalysisModule::class;
    }

    /**
     * @return self
     */
    public function getModulesThatContainsAtListOneModuleVersion(): self
    {
        $collection = new self();
        foreach ($this->elements as $module) {
            if (!$module->getModuleVersionCollection()->isEmpty()) {
                $collection->add($module);
            }
        }

        return $collection;
    }

    /**
     * @return \Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Response\Collection\HttpUpgradeAnalysisModuleVersionCollection
     */
    public function getModuleVersionCollection(): HttpUpgradeAnalysisModuleVersionCollection
    {
        $collection = new HttpUpgradeAnalysisModuleVersionCollection();

        foreach ($this->elements as $module) {
            foreach ($module->getModuleVersionCollection() as $moduleVersion) {
                $collection->add($moduleVersion);
            }
        }

        return $collection;
    }
}
