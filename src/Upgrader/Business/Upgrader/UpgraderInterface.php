<?php

/**
 * Copyright © 2021-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader;

use Upgrader\Business\Command\ResultOutput\CommandResultOutput;

interface UpgraderInterface
{
    /**
     * @return \Upgrader\Business\Command\ResultOutput\CommandResultOutput
     */
    public function upgrade(): CommandResultOutput;
}
