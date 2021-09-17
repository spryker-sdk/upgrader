<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\VersionControlSystem\Client\Git\Command;

use Upgrader\Business\Command\AbstractCommand;
use Upgrader\Business\Command\CommandInterface;

class GitAddCommand extends AbstractCommand implements CommandInterface
{
    /**
     * @return string
     */
    public function getCommand(): string
    {
        return 'git add composer.lock composer.json';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'git:add';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'The command for adding changes';
    }
}
