<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
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
