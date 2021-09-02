<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Command;

interface CommandInterface
{
    /**
     * @return string
     */
    public function getCommand(): string;

    /**
     * @param string|null $command
     *
     * @return \Upgrader\Business\Command\CommandResultInterface
     */
    public function exec(?string $command = null): CommandResultInterface;
}
