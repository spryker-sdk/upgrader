<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Services;

use Upgrade\Application\Dto\Step\StepsExecutionDto;

interface UpgraderServiceInterface
{
    /**
     * @throws \Upgrade\Infrastructure\Exception\UpgradeStrategyIsNotDefinedException
     *
     * @return \Upgrade\Application\Dto\Step\StepsExecutionDto
     */
    public function upgrade(): StepsExecutionDto;
}
