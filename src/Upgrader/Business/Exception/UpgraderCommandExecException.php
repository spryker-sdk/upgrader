<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Exception;

use Exception;

class UpgraderCommandExecException extends Exception
{
    protected const ERROR_DESCRIPTION = 'Command: [%s]' . PHP_EOL . 'Error: %s';

    /**
     * @var string
     */
    protected $message;

    /**
     * @param string $command
     * @param string $commandOutput
     */
    public function __construct(string $command, string $commandOutput)
    {
        parent::__construct(sprintf(self::ERROR_DESCRIPTION, $command, $commandOutput));
    }
}
