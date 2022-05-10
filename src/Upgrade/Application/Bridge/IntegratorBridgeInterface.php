<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Bridge;

use Upgrade\Application\Dto\StepsExecutionDto;

interface IntegratorBridgeInterface
{
    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function runIntegrator(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto;
}
