<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business;

use Upgrader\Business\Upgrader\Upgrader;
use Upgrader\Business\Upgrader\UpgraderResultInterface;

class UpgraderFacade implements UpgraderFacadeInterface
{
    /**
     * @var \Upgrader\Business\Upgrader\Upgrader
     */
    protected $upgrader;

    /**
     * @return \Upgrader\Business\Upgrader\UpgraderResultInterface
     */
    public function upgrade(): UpgraderResultInterface
    {
        return $this->getUpgrader()->upgrade();
    }

    /**
     * @return \Upgrader\Business\Upgrader\UpgraderResultInterface
     */
    public function isUpgradeAvailable(): UpgraderResultInterface
    {
        return $this->getUpgrader()->isUpgradeAvailable();
    }

    /**
     * @return \Upgrader\Business\Upgrader\Upgrader
     */
    protected function getUpgrader(): Upgrader
    {
        if ($this->upgrader === null) {
            $this->upgrader = new Upgrader();
        }

        return $this->upgrader;
    }
}
