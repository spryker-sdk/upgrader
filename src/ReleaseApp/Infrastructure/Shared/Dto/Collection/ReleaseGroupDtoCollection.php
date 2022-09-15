<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseApp\Infrastructure\Shared\Dto\Collection;

use ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto;

class ReleaseGroupDtoCollection
{
    /**
     * @var array<\ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto>
     */
    protected array $elements = [];

    /**
     * @param array<\ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto> $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * @param \ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto $element
     *
     * @return void
     */
    public function add(ReleaseGroupDto $element): void
    {
        $this->elements[] = $element;
    }

    /**
     * @return array<\ReleaseApp\Infrastructure\Shared\Dto\ReleaseGroupDto>
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
     * @param \ReleaseApp\Infrastructure\Shared\Dto\Collection\ReleaseGroupDtoCollection|self $collectionToMerge
     *
     * @return void
     */
    public function addCollection(self $collectionToMerge): void
    {
        $this->elements = array_merge($this->elements, $collectionToMerge->toArray());
    }

    /**
     * @return \ReleaseApp\Infrastructure\Shared\Dto\Collection\ModuleDtoCollection
     */
    public function getCommonModuleCollection(): ModuleDtoCollection
    {
        $resultCollection = new ModuleDtoCollection();
        foreach ($this->elements as $releaseGroup) {
            $resultCollection->addCollection($releaseGroup->getModuleCollection());
        }

        return $resultCollection;
    }
}
