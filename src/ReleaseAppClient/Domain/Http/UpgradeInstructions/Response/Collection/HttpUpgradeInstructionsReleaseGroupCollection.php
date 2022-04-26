<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\Collection;

use ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\HttpUpgradeInstructionsReleaseGroup;
use Upgrade\Infrastructure\Exception\UpgraderException;

class HttpUpgradeInstructionsReleaseGroupCollection
{
    /**
     * @var array<\ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\HttpUpgradeInstructionsReleaseGroup>
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
     * @param \ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\HttpUpgradeInstructionsReleaseGroup $element
     *
     * @return void
     */
    public function add(HttpUpgradeInstructionsReleaseGroup $element): void
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
     * @param \ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\Collection\HttpUpgradeInstructionsReleaseGroupCollection|self $collectionToMerge
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
     * @return self
     */
    public function getSortedByReleased(): self
    {
        $sortData = [];

        foreach ($this->elements as $releaseGroup) {
            $timestamp = $releaseGroup->getReleased()->getTimestamp();
            $sortData[$timestamp] = $releaseGroup;
        }

        ksort($sortData);

        $collection = new self();
        foreach ($sortData as $releaseGroup) {
            $collection->add($releaseGroup);
        }

        return $collection;
    }

    /**
     * @return self
     */
    public function filterWithoutReleased(): self
    {
        $result = new self();

        foreach ($this->elements as $releaseGroup) {
            try {
                $dateTime = $releaseGroup->getReleased();
            } catch (UpgraderException $exception) {
                $dateTime = null;
            }

            if ($dateTime) {
                $result->add($releaseGroup);
            }
        }

        return $result;
    }
}
