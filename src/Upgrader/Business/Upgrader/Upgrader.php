<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader;

use Symfony\Component\Process\Process;
use Upgrader\Business\Command\CommandInterface;
use Upgrader\Business\Command\CommandRequest;
use Upgrader\Business\Command\Response\CommandResponse;
use Upgrader\Business\Command\Response\Collection\CommandResponseCollection;

class Upgrader implements UpgraderInterface
{
    /**
     * @var \Upgrader\Business\Command\CommandInterface[]
     */
    protected $commands = [];

    /**
     * @param \Upgrader\Business\Command\CommandInterface $command
     *
     * @return $this
     */
    public function addCommand(CommandInterface $command)
    {
        $this->commands[] = $command;

        return $this;
    }

    /**
     * @return \Upgrader\Business\Command\CommandInterface[]
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * @param \Upgrader\Business\Command\CommandRequest $commandRequest
     * @return \Upgrader\Business\Command\Response\Collection\CommandResponseCollection
     */
    public function run(CommandRequest $commandRequest): CommandResponseCollection
    {
        $commandResponseList = new CommandResponseCollection();
        $commandsList = $commandRequest->getCommandFilterListAsArray();

        /** @var \Evaluator\Business\Command\CommandInterface $command */
        foreach ($this->commands as $command) {
            if ($commandsList !== [] && !in_array($command->getName(), $commandsList)) {
                continue;
            }

            $commandResponse = $command->run();
            $commandResponseList->add($commandResponse);

            if($commandResponseList->getExitCode() == CommandResponse::CODE_ERROR){
                break;
            }
        }

        return $commandResponseList;
    }
}
