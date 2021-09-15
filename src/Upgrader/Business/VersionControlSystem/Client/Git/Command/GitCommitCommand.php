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

class GitCommitCommand extends AbstractCommand implements CommandInterface
{
    /**
     * @return string
     */
    public function getCommand(): string
    {
        return 'git commit -m "upgradebot"';
    }

    public function getName(): string
    {
        return 'git:commit';
    }

    public function getDescription(): string
    {
        return 'The command for commiting changes';
    }

    public function run(): CommandResponse
    {
        return $this->runProcess($this->getCommand());
    }
}
