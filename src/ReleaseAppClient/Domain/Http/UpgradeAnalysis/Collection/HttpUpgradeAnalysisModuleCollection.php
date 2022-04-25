<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Domain\Http\UpgradeAnalysis\Collection;

use Upgrade\Domain\Dto\Collection\UpgraderCollection;
use ReleaseAppClient\Domain\Http\UpgradeAnalysis\HttpUpgradeAnalysisModule;

/**
 * @method \ReleaseAppClient\Domain\Http\UpgradeAnalysis\HttpUpgradeAnalysisModule[]|\ArrayIterator|\Traversable getIterator()
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
        foreach ($this as $module) {
            if (!$module->getModuleVersionCollection()->isEmpty()) {
                $collection->add($module);
            }
        }

        return $collection;
    }

    /**
     * @return \ReleaseAppClient\Domain\Http\UpgradeAnalysis\Collection\HttpUpgradeAnalysisModuleVersionCollection
     */
    public function getModuleVersionCollection(): HttpUpgradeAnalysisModuleVersionCollection
    {
        $collection = new HttpUpgradeAnalysisModuleVersionCollection();

        foreach ($this as $module) {
            foreach ($module->getModuleVersionCollection() as $moduleVersion) {
                $collection->add($moduleVersion);
            }
        }

        return $collection;
    }
}
