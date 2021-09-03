<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader;

class UpgraderConfig
{
    protected const UPGRADER_COMMAND_EXECUTION_TIMEOUT = 'UPGRADER_COMMAND_EXECUTION_TIMEOUT';
    protected const DEFAULT_COMMAND_EXECUTION_TIMEOUT = 600;

    /**
     * @return int
     */
    public function getCommandExecutionTimeout(): int
    {
        return (int)getenv(self::UPGRADER_COMMAND_EXECUTION_TIMEOUT) ?? static::DEFAULT_COMMAND_EXECUTION_TIMEOUT;
    }
}
