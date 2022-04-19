<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\Collection;

use Upgrade\Application\Dto\Collection\UpgraderCollection;
use Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\UpgradeInstructionModule;

/**
 * @method \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeInstructions\Response\UpgradeInstructions\UpgradeInstructionModule[]|\ArrayIterator|\Traversable getIterator()
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
