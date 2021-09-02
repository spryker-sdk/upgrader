<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business;

use Upgrader\Business\Upgrader\UpgraderResultInterface;

interface UpgraderFacadeInterface
{
    /**
     * @return \Upgrader\Business\Upgrader\UpgraderResultInterface
     */
    public function upgrade(): UpgraderResultInterface;

    /**
     * @return \Upgrader\Business\Upgrader\UpgraderResultInterface
     */
    public function isUpgradeAvailable(): UpgraderResultInterface;
}
