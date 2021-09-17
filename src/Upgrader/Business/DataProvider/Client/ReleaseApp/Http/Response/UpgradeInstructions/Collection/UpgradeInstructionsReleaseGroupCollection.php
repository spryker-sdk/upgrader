<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\Collection;

use Upgrader\Business\Collection\UpgraderCollection;
use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\UpgradeInstructionsReleaseGroup;

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

        /** @var \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\UpgradeInstructionsReleaseGroup $releaseGroup */
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
}
