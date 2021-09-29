<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Response\Collection;

use Upgrader\Business\Collection\UpgraderCollection;
use Upgrader\Business\PackageManager\Response\PackageManagerResponse;
use Upgrader\Business\Upgrader\Response\UpgraderResponseInterface;

class PackageManagerResponseCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return PackageManagerResponse::class;
    }
}
