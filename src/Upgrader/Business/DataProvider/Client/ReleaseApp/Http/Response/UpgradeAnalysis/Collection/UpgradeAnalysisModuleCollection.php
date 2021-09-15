<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\Collection;

use Upgrader\Business\Collection\UpgraderCollection;
use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\UpgradeAnalysisModule;

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
     * @return $this
     */
    public function getModulesThatContainsAtListOneModuleVersion()
    {
        $collection = new self();
        /** @var \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\UpgradeAnalysisModule $module */
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

        /** @var \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\UpgradeAnalysisModule $module */
        foreach ($this->elements as $module) {
            /** @var \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeAnalysis\UpgradeAnalysisModuleVersion $moduleVersion */
            foreach ($module->getModuleVersionCollection()->toArray() as $moduleVersion) {
                $collection->add($moduleVersion);
            }
        }

        return $collection;
    }
}
