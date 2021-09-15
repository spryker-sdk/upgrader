<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\VersionControlSystem\Client\Git\Command;

use Upgrader\Business\Command\AbstractCommand;
use Upgrader\Business\Command\CommandInterface;
use Upgrader\Business\Command\CommandResponse;

class GitUpdateIndexCommand extends AbstractCommand implements CommandInterface
{
    /**
     * @return string
     */
    public function getCommand(): string
    {
        return 'git update-index --refresh';
    }

    public function getName(): string
    {
        return 'git:uncommited';
    }

    public function getDescription(): string
    {
        return 'The command for checking uncommited changes';
    }

    public function run(): CommandResponse
    {
        return $this->runProcess($this->getCommand());
    }
}
