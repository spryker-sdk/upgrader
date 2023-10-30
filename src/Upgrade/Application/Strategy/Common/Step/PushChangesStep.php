<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\Step;

use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\RollbackStepInterface;

class PushChangesStep extends AbstractStep implements RollbackStepInterface
{
    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->vsc->pushChanges($stepsExecutionDto);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function rollBack(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        if ($stepsExecutionDto->hasErrors()) {
            return $stepsExecutionDto;
        }

        return $this->vsc->deleteRemoteBranch($stepsExecutionDto);
    }
}
