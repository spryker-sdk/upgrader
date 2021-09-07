<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business;

use Upgrader\Business\Command\ResultOutput\CommandResultOutput;

interface UpgraderFacadeInterface
{
    /**
     * Specification:
     * - Updates Spryker packages.
     * - Checks project files on existing uncommitted changes, return exit code 1 if exists.
     * - Returns command result object contains exit code and message.
     *
     * @api
     *
     * @return \Upgrader\Business\Command\ResultOutput\CommandResultOutput
     */
    public function upgrade(): CommandResultOutput;
}
