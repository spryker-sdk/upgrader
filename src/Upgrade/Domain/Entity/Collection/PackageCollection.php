<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Entity\Collection;

use Upgrade\Domain\Entity\Package;

class PackageCollection
{
    /**
     * @var array<\Upgrade\Domain\Entity\Package>
     */
    protected array $elements = [];

    /**
     * @param array<\Upgrade\Domain\Entity\Package> $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * @param \Upgrade\Domain\Entity\Package $element
     *
     * @return void
     */
    public function add(Package $element): void
    {
        $this->elements[] = $element;
    }

    /**
     * @return array<\Upgrade\Domain\Entity\Package>
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
     * @param \Upgrade\Domain\Entity\Collection\PackageCollection|self $collectionToMerge
     *
     * @return void
     */
    public function addCollection(self $collectionToMerge): void
    {
        $this->elements = array_merge($this->elements, $collectionToMerge->toArray());
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
