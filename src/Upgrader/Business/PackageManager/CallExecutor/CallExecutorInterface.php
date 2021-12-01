<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\PackageManager\CallExecutor;

use Symfony\Component\Process\Process;
use Upgrader\Business\PackageManager\Response\PackageManagerResponse;

interface CallExecutorInterface
{
    /**
     * @param string $command
     *
     * @return \Symfony\Component\Process\Process
     */
    public function runProcess(string $command): Process;

    /**
     * @param \Symfony\Component\Process\Process $process
     *
     * @return \Upgrader\Business\PackageManager\Response\PackageManagerResponse
     */
    public function createResponse(Process $process): PackageManagerResponse;
}
