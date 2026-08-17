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

    public function count(): int
    {
        return count($this->elements);
    }

    public function isEmpty(): bool
    {
        return !$this->elements;
    }

    /**
     * @param \ReleaseApp\Domain\Entities\Collection\UpgradeInstructionModuleCollection|self $collectionToMerge
     */
    public function addCollection(self $collectionToMerge): void
    {
        $this->elements = array_merge($this->elements, $collectionToMerge->toArray());
    }

    public function getByName(string $name): ?UpgradeInstructionModule
    {
        foreach ($this->elements as $module) {
            if ($module->getName() === $name) {
                return $module;
            }
        }

        return null;
    }

    public function deleteByName(string $name): void
    {
        foreach ($this->elements as $key => $module) {
            if ($module->getName() === $name) {
                unset($this->elements[$key]);
            }
        }
    }
}
