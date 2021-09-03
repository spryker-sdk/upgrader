<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Communication\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
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
