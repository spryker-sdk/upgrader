<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Strategy;

use Upgrader\Business\Upgrader\Request\UpgraderRequest;

interface UpdateStrategyGeneratorInterface
{
    /**
     * @param \Upgrader\Business\Upgrader\Request\UpgraderRequest $request
     *
     * @return \Upgrader\Business\Upgrader\Strategy\UpgradeStrategyInterface
     */
    public function getStrategy(UpgraderRequest $request): UpgradeStrategyInterface;
}
