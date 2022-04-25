<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Domain\Dto\Collection;

use Upgrade\Domain\Dto\Collection\UpgraderCollection;
use ReleaseAppClient\Domain\Dto\ModuleDto;
use ReleaseAppClient\Domain\ReleaseAppConst;

/**
 * @method \ReleaseAppClient\Domain\Dto\ModuleDto[]|\ArrayIterator|\Traversable getIterator()
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
     * @return int
     */
    public function getMajorAmount(): int
    {
        $result = 0;
        foreach ($this as $module) {
            if ($module->getVersionType() === ReleaseAppConst::MODULE_TYPE_MAJOR) {
                $result++;
            }
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getMinorAmount(): int
    {
        $result = 0;
        foreach ($this as $module) {
            if ($module->getVersionType() === ReleaseAppConst::MODULE_TYPE_MINOR) {
                $result++;
            }
        }

        return $result;
    }

    /**
     * @return int
     */
    public function getPathAmount(): int
    {
        $result = 0;
        foreach ($this as $module) {
            if ($module->getVersionType() === ReleaseAppConst::MODULE_TYPE_PATCH) {
                $result++;
            }
        }

        return $result;
    }
}
