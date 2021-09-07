<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Command;

use Upgrader\Business\Command\ResultOutput\CommandResultOutput;

interface CommandInterface
{
    /**
     * @param string|null $command
     *
     * @return \Upgrader\Business\Command\ResultOutput\CommandResultOutput
     */
    public function run(?string $command = null): CommandResultOutput;
}
