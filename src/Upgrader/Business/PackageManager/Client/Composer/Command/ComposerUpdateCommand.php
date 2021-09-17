<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\PackageManager\Client\Composer\Command;

use Upgrader\Business\Command\AbstractCommand;
use Upgrader\Business\Command\CommandInterface;

class ComposerUpdateCommand extends AbstractCommand implements CommandInterface
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'composer:update';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'The command for update';
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return 'composer update';
    }
}
