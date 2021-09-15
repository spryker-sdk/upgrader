<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Command;

use Symfony\Component\Process\Process;
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

    /**
     *
     * @return \Upgrader\Business\Command\CommandResponse
     */
    public function runProcess(string $cliCommand): CommandResponse
    {
        $process = new Process(explode(' ', $cliCommand), (string)getcwd());
        $process->setTimeout(9000);
        $process->run();
        $output = $process->getOutput();

        if(!$process->isSuccessful()){
            $output .= "\n" . $process->getErrorOutput();
        }

        return new CommandResponse($process->isSuccessful(), $this->getName(), $output);
    }
}
