<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader;

use Symfony\Component\Process\Process;
use Upgrader\Business\Command\CommandInterface;
use Upgrader\Business\Command\CommandRequest;
use Upgrader\Business\Command\CommandResponse;
use Upgrader\Business\Command\CommandResponseList;
use Upgrader\Business\Command\ResultOutput\CommandResultOutput;
use Upgrader\Business\PackageManager\PackageManagerInterface;
use Upgrader\Business\VersionControlSystem\VersionControlSystemInterface;

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
     * @return \Upgrader\Business\Command\CommandResponseList
     */
    public function run(CommandRequest $commandRequest): CommandResponseList
    {

        $commandResponseList = new CommandResponseList();
        $commandsList = $commandRequest->getCommandFilterListAsArray();

        /** @var \Evaluator\Business\Command\CommandInterface $command */
        foreach ($this->commands as $command) {
            if ($commandsList !== [] && !in_array($command->getName(), $commandsList)) {
                continue;
            }

            $commandResponse = $command->runCommand();
            $commandResponseList->add($commandResponse);

            if($commandResponseList->getExitCode() == CommandResponse::CODE_ERROR){
                break;
            }
        }

        return $commandResponseList;
    }
}
