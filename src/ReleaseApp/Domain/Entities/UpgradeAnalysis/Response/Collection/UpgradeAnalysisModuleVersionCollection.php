<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Domain\Entities\UpgradeAnalysis\Response\Collection;

use ReleaseApp\Domain\Entities\UpgradeAnalysis\Response\UpgradeAnalysisModuleVersion;

class UpgradeAnalysisModuleVersionCollection
{
    /**
     * @var array<\ReleaseApp\Domain\Entities\UpgradeAnalysis\Response\UpgradeAnalysisModuleVersion>
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
     * @param \ReleaseApp\Domain\Entities\UpgradeAnalysis\Response\UpgradeAnalysisModuleVersion $element
     *
     * @return void
     */
    public function add(UpgradeAnalysisModuleVersion $element): void
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
     * @param \ReleaseApp\Domain\Entities\UpgradeAnalysis\Response\Collection\UpgradeAnalysisModuleVersionCollection|self $collectionToMerge
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
