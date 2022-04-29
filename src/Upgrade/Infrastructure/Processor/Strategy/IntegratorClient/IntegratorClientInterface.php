<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\IntegratorClient;

use Upgrade\Infrastructure\Dto\Step\StepsExecutionDto;

interface IntegratorClientInterface
{
    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function runIntegrator(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto;
}
