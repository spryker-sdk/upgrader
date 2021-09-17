<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Entity\Collection;

use Upgrader\Business\Collection\UpgraderCollection;
use Upgrader\Business\DataProvider\Entity\ReleaseGroup;

/**
 * @method \Upgrader\Business\DataProvider\Entity\ReleaseGroup[]|\ArrayIterator|\Traversable getIterator()
 */
class ReleaseGroupCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return ReleaseGroup::class;
    }
}
