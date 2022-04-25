<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ReleaseAppClient\Domain\Http\UpgradeAnalysis\Collection;

use Upgrade\Domain\Dto\Collection\UpgraderCollection;
use ReleaseAppClient\Domain\Http\UpgradeAnalysis\HttpUpgradeAnalysisModuleVersion;

/**
 * @method \ReleaseAppClient\Domain\Http\UpgradeAnalysis\HttpUpgradeAnalysisModuleVersion[]|\ArrayIterator|\Traversable getIterator()
 */
class HttpUpgradeAnalysisModuleVersionCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return HttpUpgradeAnalysisModuleVersion::class;
    }
}
