<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy;

use Upgrade\Domain\Dto\Step\StepsExecutionDto;

interface StrategyInterface
{
    /**
     * @return string
     */
    public function getStrategyName(): string;

    /**
     * @return \Upgrade\Domain\Dto\Step\StepsExecutionDto
     */
    public function upgrade(): StepsExecutionDto;
}
