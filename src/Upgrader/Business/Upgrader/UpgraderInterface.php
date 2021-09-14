<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader;

use Upgrader\Business\Command\CommandRequest;
use Upgrader\Business\Command\CommandResponseList;
use Upgrader\Business\Command\ResultOutput\CommandResultOutput;

interface UpgraderInterface
{
    /**
     * @param \Upgrader\Business\Command\CommandRequest $commandRequest
     * @return \Upgrader\Business\Command\CommandResponseList
     */
    public function run(CommandRequest $commandRequest): CommandResponseList;
}
