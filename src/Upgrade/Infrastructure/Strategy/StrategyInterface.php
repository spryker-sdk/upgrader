<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Strategy;

use Upgrade\Application\Dto\Step\StepsExecutionDto;

interface StrategyInterface
{
    /**
     * @return string
     */
    public function getStrategyName(): string;

    /**
     * @return \Upgrade\Application\Dto\Step\StepsExecutionDto
     */
    public function upgrade(): StepsExecutionDto;
}
