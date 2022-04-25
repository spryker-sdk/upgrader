<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\Collection;

use Upgrade\Application\Dto\Collection\UpgraderCollection;
use Upgrade\Infrastructure\Exception\UpgraderException;
use ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\HttpUpgradeInstructionsReleaseGroup;

/**
 * @method \ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\HttpUpgradeInstructionsReleaseGroup[]|\ArrayIterator|\Traversable getIterator()
 */
class HttpUpgradeInstructionsReleaseGroupCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return HttpUpgradeInstructionsReleaseGroup::class;
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
