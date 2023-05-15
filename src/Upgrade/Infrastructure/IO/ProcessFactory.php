<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Infrastructure\IO;

use Symfony\Component\Process\Process;

class ProcessFactory
{
    /**
     * @param string $commandLine
     *
     * @return \Symfony\Component\Process\Process
     */
    public function createFromShellCommandline(string $commandLine): Process
    {
        return Process::fromShellCommandline($commandLine);
    }
}
