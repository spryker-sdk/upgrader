<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
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
