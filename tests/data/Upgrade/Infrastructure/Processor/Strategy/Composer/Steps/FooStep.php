<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace UpgradeData\Infrastructure\Processor\Strategy\Composer\Steps;

use Upgrade\Application\Dto\StepsExecutionDto;
use Upgrade\Application\Strategy\StepInterface;

class FooStep implements StepInterface
{
    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function run(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $stepsExecutionDto;
    }
}
