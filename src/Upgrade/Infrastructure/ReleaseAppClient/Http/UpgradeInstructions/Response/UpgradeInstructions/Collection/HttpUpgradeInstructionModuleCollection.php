<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeInstructions\Response\UpgradeInstructions\Collection;

use Upgrade\Application\Dto\Collection\UpgraderCollection;
use Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeInstructions\Response\UpgradeInstructions\HttpUpgradeInstructionModule;

/**
 * @method \Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeInstructions\Response\UpgradeInstructions\HttpUpgradeInstructionModule[]|\ArrayIterator|\Traversable getIterator()
 */
class HttpUpgradeInstructionModuleCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return HttpUpgradeInstructionModule::class;
    }
}
