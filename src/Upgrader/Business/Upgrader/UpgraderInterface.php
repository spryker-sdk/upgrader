<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Upgrader\Business\Upgrader;

use Upgrader\Business\Command\CommandRequest;
use Upgrader\Business\Command\Response\Collection\CommandResponseCollection;

interface UpgraderInterface
{
    /**
     * @param \Upgrader\Business\Command\CommandRequest $commandRequest
     *
     * @return \Upgrader\Business\Command\Response\Collection\CommandResponseCollection
     */
    public function run(CommandRequest $commandRequest): CommandResponseCollection;

    /**
     * @return \Upgrader\Business\Command\CommandInterface[]
     */
    public function getCommands(): array;
}
