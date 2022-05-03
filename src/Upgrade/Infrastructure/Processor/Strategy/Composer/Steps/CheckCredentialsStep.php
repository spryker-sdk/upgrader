<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\Composer\Steps;

use Upgrade\Infrastructure\Dto\Step\StepsExecutionDto;
use Upgrade\Infrastructure\Processor\Strategy\StepInterface;

class CheckCredentialsStep extends AbstractStep implements StepInterface
{
    /**
     * @param \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function run(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->vsc->validateSourceCodeProviderCredentials($stepsExecutionDto);
    }
}
