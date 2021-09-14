<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Command;

use Symfony\Component\Process\Process;
use Upgrader\Business\Command\ResultOutput\CommandResultOutput;
use Upgrader\UpgraderConfig;

abstract class AbstractCommand implements CommandInterface
{
    /**
     * @var \Upgrader\UpgraderConfig
     */
    protected $config;

    /**
     * @param \Upgrader\UpgraderConfig $config
     */
    public function __construct(UpgraderConfig $config)
    {
        $this->config = $config;
    }

//    /**
//     * @return \Upgrader\Business\Command\CommandResponse
//     */
//    public function runCommand(): CommandResponse
//    {
//        $process = new Process(explode(' ', $this->getCommand()), (string)getcwd());
//        $process->setTimeout(9000);
//        $process->run();
//
//        return new CommandResponse($process, $this->getName());
//    }
}
