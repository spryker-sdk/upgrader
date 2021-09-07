<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\Client\Composer\Command;

use Upgrader\Business\Command\AbstractCommand;

class ComposerUpdateCommand extends AbstractCommand
{
    protected const COMMAND_NAME = 'composer update';

    /**
     * @return string
     */
    public function getCommand(): string
    {
        return static::COMMAND_NAME;
    }
}
