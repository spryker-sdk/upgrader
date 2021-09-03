<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Command\Executor;

class CommandExecutor implements CommandExecutorInterface
{
    /**
     * @var \Upgrader\Business\Command\CommandInterface[]
     */
    protected $commands;

    /**
     * @param \Upgrader\Business\Command\CommandInterface[] $commands
     */
    public function __construct(array $commands)
    {
        $this->commands = $commands;
    }

    /**
     * @return \Upgrader\Business\Command\ResultOutput\CommandResultOutput[]
     */
    public function execute(): array
    {
        $commandResultOutputs = [];

        foreach ($this->commands as $command) {
            $commandResultOutputs[] = $command->run();
        }

        return $commandResultOutputs;
    }
}
