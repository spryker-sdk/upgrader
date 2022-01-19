<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Process;

use Symfony\Component\Process\Process;

class ProcessRunner
{
    /**
     * @param array $command
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
