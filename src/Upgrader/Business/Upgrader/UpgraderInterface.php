<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader;

use Upgrader\Business\Command\CommandRequest;
use Upgrader\Business\Command\Response\Collection\CommandResponseCollection;

interface UpgraderInterface
{
    /**
     * @param \Upgrader\Business\Command\CommandRequest $commandRequest
     * @return \Upgrader\Business\Command\Response\Collection\CommandResponseCollection
     */
    public function run(CommandRequest $commandRequest): CommandResponseCollection;
}
