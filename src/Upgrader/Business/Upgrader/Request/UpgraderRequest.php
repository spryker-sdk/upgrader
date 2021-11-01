<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Request;

use Upgrader\Business\Upgrader\Enum\UpgradeStrategyEnum;

class UpgraderRequest
{
    /**
     * @var \Upgrader\Business\Upgrader\Enum\UpgradeStrategyEnum
     */
    protected $strategy;

    /**
     * @param \Upgrader\Business\Upgrader\Enum\UpgradeStrategyEnum $strategy
     */
    public function __construct(UpgradeStrategyEnum $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @return \Upgrader\Business\Upgrader\Enum\UpgradeStrategyEnum
     */
    public function getStrategyEnum(): UpgradeStrategyEnum
    {
        return $this->strategy;
    }
}
