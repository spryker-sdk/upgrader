<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business;

use Upgrader\Business\Command\CommandResponseList;
use Upgrader\Business\Command\CommandRequest;
use Upgrader\Business\Command\ResultOutput\CommandResultOutput;

interface UpgraderFacadeInterface
{
    /**
     * Specification:
     * - Checks for uncommented changes.
     * - Updates packages to the latest available version.
     * - Returns command result object that contains exit code and message.
     *
     * @api
     *
     * @param \Upgrader\Business\Command\CommandRequest $commandRequest
     *
     * @return \Upgrader\Business\Command\CommandResponseList
     */
    public function upgrade(CommandRequest $commandRequest): CommandResponseList;

    /**
     * Specification:
     * - Return list of commands for upgrade.
     *
     * @api
     *
     * @return \Upgrader\Business\Command\CommandInterface[]
     */
    public function getUpgraderCommands(): array;
}
