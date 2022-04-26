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
     * @throws \Upgrade\Infrastructure\Exception\UpgradeStrategyIsNotDefinedException
     *
     * @return \Upgrade\Domain\Dto\Step\StepsExecutionDto
     */
    public function upgrade(): StepsExecutionDto;
}
