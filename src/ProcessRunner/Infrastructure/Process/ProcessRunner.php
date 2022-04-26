<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ProcessRunner\Infrastructure\Process;

use ProcessRunner\Application\Service\ProcessRunnerServiceInterface;
use Symfony\Component\Process\Process;

class ProcessRunner implements ProcessRunnerServiceInterface
{
    /**
     * @param array $command
     *
     * @return \Symfony\Component\Process\Process
     */
    public function runCommand(array $command): Process
    {
        var_dump(implode(' ', $command));

        $process = new Process($command, (string)getcwd());
        $process->setTimeout(300);
        $process->run();

        return $process;
    }
}
