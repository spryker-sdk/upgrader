<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy;

use Upgrade\Application\Dto\StepsResponseDto;

abstract class AbstractStrategy implements StrategyInterface
{
    /**
     * @var array<\Upgrade\Application\Strategy\StepInterface>
     */
    protected array $steps = [];

    /**
     * @var array<\Upgrade\Application\Strategy\FixerStepInterface>
     */
    protected array $fixers = [];

    /**
     * @param array<\Upgrade\Application\Strategy\StepInterface> $steps
     * @param array<\Upgrade\Application\Strategy\FixerStepInterface> $fixers
     */
    public function __construct(array $steps = [], array $fixers = [])
    {
        $this->steps = $steps;
        $this->fixers = $fixers;
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
                $stepsExecutionDto = $this->runWithFixer($step, $stepsExecutionDto);
            }
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

    /**
     * @param \Upgrade\Application\Strategy\StepInterface $step
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    protected function runWithFixer(StepInterface $step, StepsResponseDto $stepsResponseDto): StepsResponseDto
    {
        foreach ($this->fixers as $fixer) {
            if (!$fixer->isApplicable($stepsResponseDto)) {
                continue;
            }
            $stepsResponseDto = $fixer->run($stepsResponseDto);
            if ($stepsResponseDto->getIsSuccessful()) {
                $stepsResponseDto = $step->run($stepsResponseDto);
                if ($stepsResponseDto->getIsSuccessful()) {
                    break;
                }
            }
        }

        return $stepsResponseDto;
    }
}
