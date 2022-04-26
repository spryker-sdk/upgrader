<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Domain\Http\UpgradeAnalysis\Collection;

use ReleaseAppClient\Domain\Http\UpgradeAnalysis\HttpUpgradeAnalysisModuleVersion;

class HttpUpgradeAnalysisModuleVersionCollection
{
    /**
     * @var array<\ReleaseAppClient\Domain\Http\UpgradeAnalysis\HttpUpgradeAnalysisModuleVersion>
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
     * @param \ReleaseAppClient\Domain\Http\UpgradeAnalysis\HttpUpgradeAnalysisModuleVersion $element
     *
     * @return void
     */
    public function add(HttpUpgradeAnalysisModuleVersion $element): void
    {
        $this->elements[] = $element;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return !$this->elements;
    }

    /**
     * @param \ReleaseAppClient\Domain\Http\UpgradeAnalysis\Collection\HttpUpgradeAnalysisModuleVersionCollection|self $collectionToMerge
     *
     * @return void
     */
    public function addCollection(self $collectionToMerge): void
    {
        foreach ($collectionToMerge->toArray() as $element) {
            $this->add($element);
        }
    }
}
