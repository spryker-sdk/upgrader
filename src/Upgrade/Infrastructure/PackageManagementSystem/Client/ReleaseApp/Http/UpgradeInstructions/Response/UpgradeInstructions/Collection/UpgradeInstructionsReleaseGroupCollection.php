<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\Collection;

use Upgrade\Application\Dto\Collection\UpgraderCollection;
use Upgrade\Infrastructure\Exception\UpgraderException;
use Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\UpgradeInstructionsReleaseGroup;

/**
 * @method \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\UpgradeInstructionsReleaseGroup[]|\ArrayIterator|\Traversable getIterator()
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
