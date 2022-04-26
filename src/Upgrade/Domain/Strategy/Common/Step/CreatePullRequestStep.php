<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Domain\Strategy\Common\Step;

use Upgrade\Domain\Dto\Step\StepsExecutionDto;
use Upgrade\Domain\Strategy\StepInterface;

class CreatePullRequestStep extends AbstractStep implements StepInterface
{
    /**
     * @param \Upgrade\Domain\Dto\Step\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Domain\Dto\Step\StepsExecutionDto
     */
    public function run(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->vsc->createPullRequest($stepsExecutionDto);
    }
}
