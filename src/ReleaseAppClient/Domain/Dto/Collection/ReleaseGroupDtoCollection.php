<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Domain\Dto\Collection;

use Upgrade\Application\Dto\Collection\UpgraderCollection;
use ReleaseAppClient\Domain\Dto\ReleaseGroupDto;

/**
 * @method \ReleaseAppClient\Domain\Dto\ReleaseGroupDto[]|\ArrayIterator|\Traversable getIterator()
 */
class ReleaseGroupDtoCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return ReleaseGroupDto::class;
    }

    /**
     * @return ModuleDtoCollection
     */
    public function getCommonModuleCollection(): ModuleDtoCollection
    {
        $resultCollection = new ModuleDtoCollection();
        foreach ($this as $releaseGroup) {
            $resultCollection->addCollection($releaseGroup->getModuleCollection());
        }

        return $resultCollection;
    }
}
