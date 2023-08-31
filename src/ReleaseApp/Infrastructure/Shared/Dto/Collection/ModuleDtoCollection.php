<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace ReleaseApp\Infrastructure\Shared\Dto\Collection;

use ReleaseApp\Infrastructure\Shared\Dto\ModuleDto;

class ModuleDtoCollection
{
    /**
     * @var array<\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto>
     */
    protected array $elements = [];

    /**
     * @param array<\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto> $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ModuleDto $element
     *
     * @return void
     */
    public function add(ModuleDto $element): void
    {
        $this->elements[] = $element;
    }

    /**
     * @return array<\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto>
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
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection|self $collectionToMerge
     *
     * @return void
     */
    public function addCollection(self $collectionToMerge): void
    {
        $this->elements = array_merge($this->elements, $collectionToMerge->toArray());
    }

    /**
     * @return array<\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto>
     */
    public function getMajors(): array
    {
        return array_values(array_filter($this->elements, static fn (ModuleDto $module): bool => $module->isMajor()));
    }

    /**
     * @return array<\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto>
     */
    public function getBetaMajors(): array
    {
        return array_values(array_filter($this->elements, static fn (ModuleDto $module): bool => $module->isBetaMajor()));
    }

    /**
     * @return array<\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto>
     */
    public function getMinors(): array
    {
        return array_values(array_filter($this->elements, static fn (ModuleDto $module): bool => $module->isMinor()));
    }

    /**
     * @return array<\ReleaseApp\Infrastructure\Shared\Dto\ModuleDto>
     */
    public function getPatches(): array
    {
        return array_values(array_filter($this->elements, static fn (ModuleDto $module): bool => $module->isPatch()));
    }
}
