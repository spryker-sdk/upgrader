<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Domain\Entities\Collection;

use ReleaseApp\Domain\Entities\UpgradeInstructionModule;

class UpgradeInstructionModuleCollection
{
    /**
     * @var array<\ReleaseApp\Domain\Entities\UpgradeInstructionModule>
     */
    protected array $elements = [];

    /**
     * @param array<\ReleaseApp\Domain\Entities\UpgradeInstructionModule> $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * @param \ReleaseApp\Domain\Entities\UpgradeInstructionModule $element
     *
     * @return void
     */
    public function add(UpgradeInstructionModule $element): void
    {
        $this->elements[] = $element;
    }

    /**
     * @return array<\ReleaseApp\Domain\Entities\UpgradeInstructionModule>
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
     * @param \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection|self $collectionToMerge
     *
     * @return void
     */
    public function addCollection(self $collectionToMerge): void
    {
        $this->elements = array_merge($this->elements, $collectionToMerge->toArray());
    }
}
