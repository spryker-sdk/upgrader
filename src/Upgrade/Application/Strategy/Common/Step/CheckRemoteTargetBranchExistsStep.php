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

class CheckRemoteTargetBranchExistsStep extends AbstractStep implements StepInterface
{
    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsExecutionDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function run(StepsResponseDto $stepsExecutionDto): StepsResponseDto
    {
        $stepsExecutionDto = $this->vsc->isRemoteTargetBranchNotExist($stepsExecutionDto);
        if (!$stepsExecutionDto->getIsSuccessful()) {
            $stepsExecutionDto->setError(
                Error::createClientCodeError('You have an unprocessed PR from a previous update. Upgrader can\'t provide a new update until you process these changes'),
            );

            return $stepsExecutionDto;
        }

        return $stepsExecutionDto;
    }
}
