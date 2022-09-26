<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Core\Infrastructure\Service;

use Symfony\Component\Process\Process;

interface ProcessRunnerServiceInterface
{
    /**
     * @param array<string> $command
     *
     * @return \Symfony\Component\Process\Process<string, string>
     */
    public function run(array $command): Process;
}
