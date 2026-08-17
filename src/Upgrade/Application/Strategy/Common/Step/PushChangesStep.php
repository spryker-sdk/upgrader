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
    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->vsc->pushChanges($stepsExecutionDto);
    }

    public function rollBack(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        return $this->vsc->deleteRemoteBranch($stepsExecutionDto);
    }
}
