<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Dto\PackageManager\Collection;

use Upgrade\Application\Dto\Collection\UpgraderCollection;
use Upgrade\Application\Dto\PackageManager\PackageDto;

/**
 * @method \Upgrade\Application\Dto\PackageManager\PackageDto[]|\ArrayIterator|\Traversable getIterator()
 */
class PackageDtoCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return PackageDto::class;
    }

    /**
     * @return array
     */
    public function getNameList(): array
    {
        $result = [];

        foreach ($this as $package) {
            $result[] = (string)$package;
        }

        return $result;
    }
}
