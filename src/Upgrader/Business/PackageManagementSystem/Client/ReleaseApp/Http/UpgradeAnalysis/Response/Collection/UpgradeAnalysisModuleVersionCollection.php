<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\Collection;

use Upgrader\Business\Collection\UpgraderCollection;
use Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\UpgradeAnalysisModuleVersion;

/**
 * @method \Upgrader\Business\PackageManagementSystem\Client\ReleaseApp\Http\UpgradeAnalysis\Response\UpgradeAnalysisModuleVersion[]|\ArrayIterator|\Traversable getIterator()
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
