<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\VersionControlSystem\Client\Git\Command;

use Symfony\Component\Process\Process;
use Upgrader\Business\Command\AbstractCommand;
use Upgrader\Business\Command\CommandInterface;
use Upgrader\Business\Command\CommandResponse;

class GitAddCommand implements CommandInterface
{
    /**
     * @return string
     */
    public function getCommand(): string
    {
        return 'git add composer.lock composer.json';
    }

    public function getName(): string
    {
        return 'git:add';
    }

    public function getDescription(): string
    {
        return 'The command for adding changes';
    }

    /**
     *
     * @return \Upgrader\Business\Command\CommandResponse
     */
    public function runCommand(): CommandResponse
    {
        $process = new Process(explode(' ', $this->getCommand()), (string)getcwd());
        $process->setTimeout(9000);
        $process->run();

        return new CommandResponse($process, $this->getName());
    }
}
