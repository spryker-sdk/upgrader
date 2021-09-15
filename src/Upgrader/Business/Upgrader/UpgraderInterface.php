<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader;

use Upgrader\Business\Command\ResultOutput\Collection\CommandResultOutputCollection;

interface UpgraderInterface
{
    /**
     * @return \Upgrader\Business\Command\ResultOutput\Collection\CommandResultOutputCollection
     */
    public function upgrade(): CommandResultOutputCollection;
}
