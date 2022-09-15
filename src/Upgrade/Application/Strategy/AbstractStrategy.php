<?php

declare(strict_types=1);

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy;

use Upgrade\Application\Dto\StepsResponseDto;

abstract class AbstractStrategy implements StrategyInterface
{
    /**
     * @var array<\Upgrade\Application\Strategy\StepInterface>
     */
    protected array $steps = [];

    /**
     * @param array<\Upgrade\Application\Strategy\StepInterface> $steps
     */
    public function __construct(array $steps = [])
    {
        $this->steps = $steps;
    }

    /**
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function upgrade(): StepsResponseDto
    {
        $executedSteps = [];
        $stepsExecutionDto = new StepsResponseDto(true);

        foreach ($this->steps as $step) {
            $executedSteps[] = $step;

            $stepsExecutionDto = $step->run($stepsExecutionDto);

            if (!$stepsExecutionDto->getIsSuccessful()) {
                $rollBackExecutionDto = new StepsResponseDto(true);
                foreach (array_reverse($executedSteps) as $executedStep) {
                    if ($executedStep instanceof RollbackStepInterface) {
                        $rollBackExecutionDto = $executedStep->rollBack($rollBackExecutionDto);
                    }
                }

                return $stepsExecutionDto;
            }
        }

        return $stepsExecutionDto;
    }
}
