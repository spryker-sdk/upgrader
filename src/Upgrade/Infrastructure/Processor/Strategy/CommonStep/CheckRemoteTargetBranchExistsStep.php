<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\CommonStep;

use Upgrade\Application\Dto\Step\StepsExecutionDto;
use Upgrade\Infrastructure\Processor\Strategy\StepInterface;

class CheckRemoteTargetBranchExistsStep extends AbstractStep implements StepInterface
{
    /**
     * @param \Upgrade\Application\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\Step\StepsExecutionDto
     */
    public function run(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $stepsExecutionDto = $this->vsc->isRemoteTargetBranchNotExist($stepsExecutionDto);
        if (!$stepsExecutionDto->getIsSuccessful()) {
            $stepsExecutionDto->setOutputMessage("You have an unprocessed PR from a previous update. Upgrader can't provide a new update until you process these changes");

            return $stepsExecutionDto;
        }

        return $stepsExecutionDto;
    }
}
