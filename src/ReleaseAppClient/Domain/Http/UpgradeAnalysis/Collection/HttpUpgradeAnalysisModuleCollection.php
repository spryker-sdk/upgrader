<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Domain\Http\UpgradeAnalysis\Collection;

use ReleaseAppClient\Domain\Http\UpgradeAnalysis\HttpUpgradeAnalysisModule;

class HttpUpgradeAnalysisModuleCollection
{
    /**
     * @var array<\ReleaseAppClient\Domain\Http\UpgradeAnalysis\HttpUpgradeAnalysisModule>
     */
    protected $elements = [];

    /**
     * @param array $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * @param \ReleaseAppClient\Domain\Http\UpgradeAnalysis\HttpUpgradeAnalysisModule $element
     *
     * @return void
     */
    public function add(HttpUpgradeAnalysisModule $element): void
    {
        $this->elements[] = $element;
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
     * @return \ReleaseAppClient\Domain\Http\UpgradeAnalysis\Collection\HttpUpgradeAnalysisModuleVersionCollection
     */
    public function getModuleVersionCollection(): HttpUpgradeAnalysisModuleVersionCollection
    {
        $collection = new HttpUpgradeAnalysisModuleVersionCollection();

        foreach ($this->elements as $module) {
            foreach ($module->getModuleVersionCollection()->toArray() as $moduleVersion) {
                $collection->add($moduleVersion);
            }
        }

        return $collection;
    }
}
