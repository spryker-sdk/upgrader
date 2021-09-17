<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Communication\Command;

use Symfony\Component\Console\Command\Command;
use Upgrader\Business\UpgraderFacade;
use Upgrader\Business\UpgraderFacadeInterface;

abstract class AbstractCommand extends Command
{
    public const CODE_SUCCESS = 0;
    public const CODE_ERROR = 1;

    /**
     * @var \Upgrader\Business\UpgraderFacadeInterface
     */
    protected $facade;

    /**
     * @return \Upgrader\Business\UpgraderFacadeInterface
     */
    protected function getFacade(): UpgraderFacadeInterface
    {
        if ($this->facade === null) {
            $this->facade = new UpgraderFacade();
        }

        return $this->facade;
    }
}
