<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\Collection;

use ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\HttpUpgradeInstructionModule;

class HttpUpgradeInstructionModuleCollection
{
    /**
     * @var array<\ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\HttpUpgradeInstructionModule>
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
     * @param \ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\HttpUpgradeInstructionModule $element
     *
     * @return void
     */
    public function add(HttpUpgradeInstructionModule $element): void
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
     * @param \ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\Collection\HttpUpgradeInstructionModuleCollection|self $collectionToMerge
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
