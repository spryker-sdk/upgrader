<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\CommonStep;

use Upgrade\Application\Dto\Step\StepsExecutionDto;
use Upgrade\Infrastructure\Processor\Strategy\RollbackStepInterface;

class CreateBranchStep extends AbstractStep implements RollbackStepInterface
{
    /**
     * @param \Upgrade\Application\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\Step\StepsExecutionDto
     */
    public function run(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->vsc->createBranch($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\Step\StepsExecutionDto
     */
    public function rollBack(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        $stepsExecutionDto = $this->vsc->checkout($stepsExecutionDto);
        $stepsExecutionDto = $this->vsc->deleteLocalBranch($stepsExecutionDto);

        return $stepsExecutionDto;
    }
}
