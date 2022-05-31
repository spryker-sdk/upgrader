<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace UpgradeData\Infrastructure\Processor\Strategy\Composer\Steps;

use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\StepInterface;

class FooStep implements StepInterface
{
    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $stepsExecutionDto;
    }
}
