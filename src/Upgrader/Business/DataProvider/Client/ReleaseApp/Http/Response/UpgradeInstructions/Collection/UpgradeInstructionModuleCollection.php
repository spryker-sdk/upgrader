<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\Collection;

use Upgrader\Business\Collection\UpgraderCollection;
use Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\UpgradeInstructionModule;

/**
 * @method \Upgrader\Business\DataProvider\Client\ReleaseApp\Http\Response\UpgradeInstructions\UpgradeInstructionModule[]|\ArrayIterator|\Traversable getIterator()
 */
class UpgradeInstructionModuleCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return UpgradeInstructionModule::class;
    }
}
