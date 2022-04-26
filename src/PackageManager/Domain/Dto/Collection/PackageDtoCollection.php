<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PackageManager\Domain\Dto\Collection;

use PackageManager\Domain\Dto\PackageDto;

class PackageDtoCollection
{
    /**
     * @var array<\PackageManager\Domain\Dto\PackageDto>
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
     * @param \PackageManager\Domain\Dto\PackageDto $element
     *
     * @return void
     */
    public function add(PackageDto $element): void
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
     * @param \PackageManager\Domain\Dto\Collection\PackageDtoCollection|self $collectionToMerge
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
     * @return array
     */
    public function getNameList(): array
    {
        $result = [];

        foreach ($this->elements as $package) {
            $result[] = (string)$package;
        }

        return $result;
    }
}
