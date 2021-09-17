<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\VersionControlSystem\Client\Git\Command;

use Upgrader\Business\Command\AbstractCommand;
use Upgrader\Business\Command\CommandInterface;

class GitUpdateIndexCommand extends AbstractCommand implements CommandInterface
{
    /**
     * @return string
     */
    public function getCommand(): string
    {
        return 'git update-index --refresh';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'git:uncommited';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'The command for checking uncommited changes';
    }
}
