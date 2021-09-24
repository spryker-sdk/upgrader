<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\Collection;

use Upgrader\Business\Collection\UpgraderCollection;
use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\UpgradeInstructionsReleaseGroup;
use Upgrader\Business\Exception\UpgraderException;

/**
 * @method \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\UpgradeInstructionsReleaseGroup[]|\ArrayIterator|\Traversable getIterator()
 */
class UpgradeInstructionsReleaseGroupCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return UpgradeInstructionsReleaseGroup::class;
    }

    /**
     * @return self
     */
    public function getSortedByReleased(): self
    {
        $sortData = [];

        foreach ($this as $releaseGroup) {
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

        foreach ($this as $releaseGroup) {
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
