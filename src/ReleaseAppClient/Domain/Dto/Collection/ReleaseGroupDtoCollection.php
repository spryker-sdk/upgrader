<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Domain\Dto\Collection;

use ReleaseAppClient\Domain\Dto\ReleaseGroupDto;

class ReleaseGroupDtoCollection
{
    /**
     * @var array<\ReleaseAppClient\Domain\Dto\ReleaseGroupDto>
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
     * @param \ReleaseAppClient\Domain\Dto\ReleaseGroupDto $element
     *
     * @return void
     */
    public function add(ReleaseGroupDto $element): void
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
     * @param \ReleaseAppClient\Domain\Dto\Collection\ReleaseGroupDtoCollection|self $collectionToMerge
     *
     * @return void
     */
    public function addCollection(self $collectionToMerge): void
    {
        foreach ($collectionToMerge->toArray() as $element) {
            $this->add($element);
        }
    }

    /**
     * @return \ReleaseAppClient\Domain\Dto\Collection\ModuleDtoCollection
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
