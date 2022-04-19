<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Dto\PackageManagementSystem\Collection;

use Upgrade\Application\Dto\Collection\UpgraderCollection;
use Upgrade\Application\Dto\PackageManagementSystem\ReleaseGroupDto;

/**
 * @method \Upgrade\Application\Dto\PackageManagementSystem\ReleaseGroupDto[]|\ArrayIterator|\Traversable getIterator()
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
}
