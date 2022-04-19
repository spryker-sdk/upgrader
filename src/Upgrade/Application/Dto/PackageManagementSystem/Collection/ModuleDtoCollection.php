<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Dto\PackageManagementSystem\Collection;

use Upgrade\Application\Dto\Collection\UpgraderCollection;
use Upgrade\Application\Dto\PackageManagementSystem\ModuleDto;
use Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\ReleaseAppConst;

/**
 * @method \Upgrade\Application\Dto\PackageManagementSystem\ModuleDto[]|\ArrayIterator|\Traversable getIterator()
 */
class ModuleDtoCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return ModuleDto::class;
    }

    /**
     * @return bool
     */
    public function isContainsMajorUpdates(): bool
    {
        foreach ($this as $module) {
            if ($module->getVersionType() === ReleaseAppConst::MODULE_TYPE_MAJOR) {
                return true;
            }
        }

        return false;
    }
}
