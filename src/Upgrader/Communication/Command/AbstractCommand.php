<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Communication\Command;

use Symfony\Component\Console\Command\Command;
use Upgrader\Business\UpgraderFacade;

class AbstractCommand extends Command
{
    /**
     * @var \Upgrader\Business\UpgraderFacade
     */
    protected $facade;

    /**
     * @return \Upgrader\Business\UpgraderFacade
     */
    protected function getFacade(): UpgraderFacade
    {
        if ($this->facade === null) {
            $this->facade = new UpgraderFacade();
        }

        return $this->facade;
    }
}
