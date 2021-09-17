<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business;

use Upgrader\Business\Command\CommandRequest;
use Upgrader\Business\Command\Response\Collection\CommandResponseCollection;

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
     * @return \Upgrader\Business\Command\Response\Collection\CommandResponseCollection
     */
    public function upgrade(CommandRequest $commandRequest): CommandResponseCollection;

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
