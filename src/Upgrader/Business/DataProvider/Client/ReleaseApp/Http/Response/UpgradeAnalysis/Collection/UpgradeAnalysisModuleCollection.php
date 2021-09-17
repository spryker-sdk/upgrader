<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\Collection;

use Upgrader\Business\Collection\UpgraderCollection;
use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\UpgradeAnalysisModule;

/**
 * @method \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\UpgradeAnalysisModule[]|\ArrayIterator|\Traversable getIterator()
 */
class UpgradeAnalysisModuleCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return UpgradeAnalysisModule::class;
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
     * @return \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\Collection\UpgradeAnalysisModuleVersionCollection
     */
    public function getModuleVersionCollection(): UpgradeAnalysisModuleVersionCollection
    {
        $collection = new UpgradeAnalysisModuleVersionCollection();

        foreach ($this->elements as $module) {
            foreach ($module->getModuleVersionCollection() as $moduleVersion) {
                $collection->add($moduleVersion);
            }
        }

        return $collection;
    }
}
