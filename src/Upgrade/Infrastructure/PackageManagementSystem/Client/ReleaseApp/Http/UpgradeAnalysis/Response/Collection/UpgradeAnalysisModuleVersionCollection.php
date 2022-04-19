<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\Collection;

use Upgrade\Application\Dto\Collection\UpgraderCollection;
use Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\UpgradeAnalysisModuleVersion;

/**
 * @method \Upgrade\Infrastructure\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\UpgradeAnalysisModuleVersion[]|\ArrayIterator|\Traversable getIterator()
 */
class UpgradeAnalysisModuleVersionCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return UpgradeAnalysisModuleVersion::class;
    }
}
