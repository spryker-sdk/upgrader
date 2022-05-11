<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Core\Infrastructure\Service;

use Symfony\Component\Process\Process;

class ProcessRunnerService implements ProcessRunnerServiceInterface
{
    /**
     * @param array<string> $command
     *
     * @return \Symfony\Component\Process\Process
     */
    public function run(array $command): Process
    {
        $process = new Process($command, (string)getcwd());
        $process->setTimeout(300);
        $process->run();

        return $process;
    }
}
