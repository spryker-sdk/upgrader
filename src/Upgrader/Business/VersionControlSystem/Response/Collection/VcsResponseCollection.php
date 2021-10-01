<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\VersionControlSystem\Response\Collection;

use Upgrader\Business\Collection\UpgraderCollection;
use Upgrader\Business\VersionControlSystem\Response\VcsResponse;

class VcsResponseCollection extends UpgraderCollection
{
    /**
     * @return string
     */
    protected function getClassName(): string
    {
        return VcsResponse::class;
    }
}
