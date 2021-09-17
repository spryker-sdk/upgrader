<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
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
