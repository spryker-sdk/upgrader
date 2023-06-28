<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Executor;

use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\RollbackStepInterface;
use Upgrade\Application\Strategy\StepInterface;

class StepExecutor implements StepExecutorInterface
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
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function execute(StepsResponseDto $stepsResponseDto): StepsResponseDto
    {
        $executedSteps = [];
        foreach ($this->steps as $step) {
            $executedSteps[] = $step;

            $stepsResponseDto = $step->run($stepsResponseDto);
            if (!$stepsResponseDto->getIsSuccessful()) {
                $stepsResponseDto = $this->runWithFixer($step, $stepsResponseDto);
            }

            if ($stepsResponseDto->isSuccessful() && $stepsResponseDto->getIsStopPropagation()) {
                return $stepsResponseDto;
            }

            if (!$stepsResponseDto->getIsSuccessful()) {
                $stepsResponseDto->addOutputMessage(sprintf('Step `%s` is failed', $this->getStepName($step)));
                $rollBackExecutionDto = new StepsResponseDto(true);
                foreach (array_reverse($executedSteps) as $executedStep) {
                    if ($executedStep instanceof RollbackStepInterface) {
                        $rollBackExecutionDto = $executedStep->rollBack($rollBackExecutionDto);
                    }
                }

                return $stepsResponseDto;
            }
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

            $stepsResponseDto->setError(null);

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
