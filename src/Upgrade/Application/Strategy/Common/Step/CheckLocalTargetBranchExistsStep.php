<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\Common\Step;

use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\StepInterface;
use Upgrade\Domain\ValueObject\Error;

class CheckLocalTargetBranchExistsStep extends AbstractStep implements StepInterface
{
    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $stepsExecutionDto = $this->vsc->isLocalTargetBranchNotExist($stepsExecutionDto);
        if (!$stepsExecutionDto->getIsSuccessful()) {
            $stepsExecutionDto->setError(
                Error::createInternalError('You have an unprocessed local branch from previous start'),
            );

            return $stepsExecutionDto;
        }

        return $stepsExecutionDto;
    }
}
