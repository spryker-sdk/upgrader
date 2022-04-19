<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\Collection;

use Upgrade\Application\Dto\Collection\UpgraderCollection;
use Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\UpgradeAnalysisModule;

/**
 * @method \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\UpgradeAnalysisModule[]|\ArrayIterator|\Traversable getIterator()
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
     * @return \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\Collection\UpgradeAnalysisModuleVersionCollection
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
