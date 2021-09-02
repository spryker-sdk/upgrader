<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader;

class UpgraderConfig
{
    public const COMMAND_EXECUTE_TIMEOUT_KEY = 'UPGRADER_COMMAND_EXECUTE_TIMEOUT';

    /**
     * @return int
     */
    public function getCommandExecuteTimeout(): int
    {
        return (int)getenv(self::COMMAND_EXECUTE_TIMEOUT_KEY) ?? 600;
    }
}
