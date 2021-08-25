<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\CommandExecutor;

interface CommandExecutorInterface
{
    /**
     * @return string
     */
    public function getCommand(): string;

    /**
     * @return int
     */
    public function getRequestTimeout(): int;
}
