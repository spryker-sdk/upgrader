<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business;

use Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection;

interface UpgraderFacadeInterface
{
    /**
     * Specification:
     * - Checks for uncommented changes.
     * - Updates packages to the latest available version.
     * - Returns command result object that contains exit code and message.
     *
     * @api
     *
     * @return \Upgrader\Business\Upgrader\Response\Collection\UpgraderResponseCollection
     */
    public function upgrade(): UpgraderResponseCollection;
}
