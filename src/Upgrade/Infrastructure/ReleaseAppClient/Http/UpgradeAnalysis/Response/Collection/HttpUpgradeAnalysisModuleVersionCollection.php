<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Response\Collection;

use Upgrade\Application\Dto\Collection\UpgraderCollection;
use Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Response\HttpUpgradeAnalysisModuleVersion;

/**
 * @method \Upgrade\Infrastructure\ReleaseAppClient\Http\UpgradeAnalysis\Response\HttpUpgradeAnalysisModuleVersion[]|\ArrayIterator|\Traversable getIterator()
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
