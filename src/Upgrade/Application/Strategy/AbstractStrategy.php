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
        $stepsResponseDto = new StepsResponseDto(true);

        foreach ($this->steps as $index => $step) {
            $stepsResponseDto->addOutputMessage(
                sprintf('%sStart executing "%s" step', $index === 0 ? '' : PHP_EOL, $this->getStepName($step)),
            );

            $executedSteps[] = $step;

            $stepsResponseDto = $step->run($stepsResponseDto);

            if (!$stepsResponseDto->getIsSuccessful()) {
                $stepsResponseDto = $this->runWithFixer($step, $stepsResponseDto);
            }
            if (!$stepsResponseDto->getIsSuccessful()) {
                $stepsResponseDto->addOutputMessage('Step is failed');
                $rollBackExecutionDto = new StepsResponseDto(true);
                foreach (array_reverse($executedSteps) as $executedStep) {
                    if ($executedStep instanceof RollbackStepInterface) {
                        $rollBackExecutionDto = $executedStep->rollBack($rollBackExecutionDto);
                    }
                }

                return $stepsResponseDto;
            }

            $stepsResponseDto->addOutputMessage('Step is successfully executed');
        }

        return $stepsResponseDto;
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
            $stepsResponseDto->addOutputMessage('Step is failed. It will be reapplied with a fixer');

            $stepsResponseDto = $fixer->run($stepsResponseDto);
            if (!$stepsResponseDto->getIsSuccessful()) {
                continue;
            }
            $stepsResponseDto = $step->run($stepsResponseDto);
            if ($stepsResponseDto->getIsSuccessful()) {
                break;
            }
        }

        return $stepsResponseDto;
    }

    /**
     * @param \Upgrade\Application\Strategy\StepInterface $step
     *
     * @return string
     */
    protected function getStepName(StepInterface $step): string
    {
        $classParts = explode('\\', get_class($step));

        return ucfirst(strtolower(trim((string)preg_replace(
            '/(?=[A-Z])/',
            ' $1',
            (string)preg_replace('/Step$/', '', end($classParts)),
        ))));
    }
}
