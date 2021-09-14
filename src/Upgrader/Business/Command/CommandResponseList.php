<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Command;

class CommandResponseList
{
    /**
     * @var int
     */
    protected $exitCode = CommandResponse::CODE_SUCCESS;

    /**
     * @var \Upgrader\Business\Command\CommandResponse[]
     */
    protected $commandResponses = [];


    /**
     * @var string|null
     */
    protected $gitBranch = null;

    /**
     * @return int
     */
    public function getExitCode(): int
    {
        return $this->exitCode;
    }

    /**
     * @return \Upgrader\Business\Command\CommandResponse[]
     */
    public function getCommandResponses(): array
    {
        return $this->commandResponses;
    }

    /**
     * @param \Upgrader\Business\Command\CommandResponse $commandResponse
     *
     * @return void
     */
    public function add(CommandResponse $commandResponse): void
    {
        if ($commandResponse->getExitCode() == CommandResponse::CODE_ERROR) {
            $this->exitCode = CommandResponse::CODE_ERROR;
        }

        $this->commandResponses[] = $commandResponse;
    }
}
