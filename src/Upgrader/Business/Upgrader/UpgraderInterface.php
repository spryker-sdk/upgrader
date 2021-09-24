<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader;

use Upgrader\Business\Command\Response\Collection\CommandResponseCollection;

interface UpgraderInterface
{
    /**
     * @return \Upgrader\Business\Command\Response\Collection\CommandResponseCollection
     */
    public function upgrade(): CommandResponseCollection;
}
