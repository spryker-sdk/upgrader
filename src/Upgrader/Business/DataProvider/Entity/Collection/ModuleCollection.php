<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Entity\Collection;

use Upgrader\Business\Collection\UpgraderCollection;
use Upgrader\Business\DataProvider\Client\ReleaseApp\ReleaseAppConst;
use Upgrader\Business\DataProvider\Entity\Module;

/**
 * @method \Upgrader\Business\DataProvider\Entity\Module[]|\ArrayIterator|\Traversable getIterator()
 */
class ModuleCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return Module::class;
    }

    /**
     * @return bool
     */
    public function isContainsMajorUpdates(): bool
    {
        foreach ($this as $module) {
            if ($module->getVersionType() == ReleaseAppConst::MODULE_TYPE_MAJOR) {
                return true;
            }
        }

        return false;
    }
}
