<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Command;

use Upgrader\Business\Command\ResultOutput\CommandResultOutput;

interface CommandInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @return string
     */
    public function getCommand(): string;

    /**
     * @return \Upgrader\Business\Command\CommandResponse
     */
    public function run(): CommandResponse;
}
