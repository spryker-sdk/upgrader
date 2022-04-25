<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Service;

use Upgrade\Domain\Dto\Step\StepsExecutionDto;

interface UpgraderServiceInterface
{
    /**
     * @return \Upgrade\Domain\Dto\Step\StepsExecutionDto
     *@throws \Upgrade\Infrastructure\Exception\UpgradeStrategyIsNotDefinedException
     *
     */
    public function upgrade(): StepsExecutionDto;
}
