<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Command\Executor;

interface CommandExecutorInterface
{
    /**
     * @return \Upgrader\Business\Command\ResultOutput\CommandResultOutput[]
     */
    public function execute(): array;
}
