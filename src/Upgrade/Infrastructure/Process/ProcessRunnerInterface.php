<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Process;

use Symfony\Component\Process\Process;

interface ProcessRunnerInterface
{
    /**
     * @param array $command
     *
     * @return \Symfony\Component\Process\Process
     */
    public function runProcess(array $command): Process;
}
