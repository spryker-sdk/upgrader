<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\Collection;

use Upgrade\Domain\Dto\Collection\UpgraderCollection;
use ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\HttpUpgradeInstructionModule;

/**
 * @method \ReleaseAppClient\Domain\Http\UpgradeInstructions\Response\HttpUpgradeInstructionModule[]|\ArrayIterator|\Traversable getIterator()
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
