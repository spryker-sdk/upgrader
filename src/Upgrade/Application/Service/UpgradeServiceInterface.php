<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Service;

use Upgrade\Application\Dto\StepsExecutionDto;

interface UpgradeServiceInterface
{
    /**
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function upgrade(): StepsExecutionDto;
}
