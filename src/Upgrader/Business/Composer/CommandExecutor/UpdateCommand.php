<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Composer\CommandExecutor;

use Upgrader\Business\CommandExecutor\AbstractCommandExecutor;

class UpdateCommand extends AbstractCommandExecutor
{
    /**
     * @return string
     */
    public function getCommand(): string
    {
        return 'composer update';
    }

    /**
     * @return int
     */
    public function getRequestTimeout(): int
    {
        return 600;
    }
}
