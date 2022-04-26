<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace ProcessRunner\Application\Service;

use Symfony\Component\Process\Process;

interface ProcessRunnerServiceInterface
{
    /**
     * @param array $command
     *
     * @return \Symfony\Component\Process\Process
     */
    public function runCommand(array $command): Process;
}
