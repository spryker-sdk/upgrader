<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Strategy;

use Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection;

interface UpgradeStrategyInterface
{
    /**
     * @return \Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection
     */
    public function upgrade(): UpgraderResponseCollection;
}
