<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\CommandExecutor;

use Symfony\Component\Process\Process;

class CommandResultBuilder
{
    /**
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \Upgrader\Business\CommandExecutor\CommandResultDto
     */
    public function createResult(Process $process): CommandResultDto
    {
        $resultOutput = $process->getExitCode() ? $process->getErrorOutput() : $process->getExitCodeText();

        return new CommandResultDto($process->getExitCode(), $resultOutput);
    }
}
