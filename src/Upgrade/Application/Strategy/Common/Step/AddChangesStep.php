<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy\Common\Step;

use Upgrade\Application\Dto\StepsExecutionDto;
use Upgrade\Application\Strategy\StepInterface;

class AddChangesStep extends AbstractStep implements StepInterface
{
    /**
     * @param \Upgrade\Application\Dto\StepsExecutionDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsExecutionDto
     */
    public function run(StepsExecutionDto $stepsExecutionDto): StepsExecutionDto
    {
        return $this->vsc->addChanges($stepsExecutionDto);
    }
}