<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Domain\Entities\Collection;

use ReleaseApp\Domain\Entities\UpgradeAnalysisModuleVersion;

class UpgradeAnalysisModuleVersionCollection
{
    /**
     * @var array<\ReleaseApp\Domain\Entities\UpgradeAnalysisModuleVersion>
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
     * @param \ReleaseApp\Domain\Entities\UpgradeAnalysisModuleVersion $element
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
     * @param \ReleaseApp\Domain\Entities\Collection\UpgradeAnalysisModuleVersionCollection|self $collectionToMerge
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
