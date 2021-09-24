<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrader\Business\Upgrader\Manager;

use Upgrader\Business\DataProvider\Response\DataProviderResponse;

interface DataProviderRequestManagerInterface
{
    /**
     * @return \Upgrader\Business\DataProvider\Response\DataProviderResponse
     */
    public function getNotInstalledReleaseGroupList(): DataProviderResponse;
}
