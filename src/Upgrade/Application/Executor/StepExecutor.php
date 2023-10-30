<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Executor;

use Psr\Log\LoggerInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Strategy\RollbackStepInterface;
use Upgrade\Application\Strategy\StepInterface;

class StepExecutor implements StepExecutorInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var array<\Upgrade\Application\Strategy\StepInterface>
     */
    protected array $steps = [];

    /**
     * @var array<\Upgrade\Application\Strategy\FixerStepInterface>
     */
    protected array $fixers = [];

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param array<\Upgrade\Application\Strategy\StepInterface> $steps
     * @param array<\Upgrade\Application\Strategy\FixerStepInterface> $fixers
     */
    public function __construct(LoggerInterface $logger, array $steps = [], array $fixers = [])
    {
        $this->logger = $logger;
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

            $this->logger->info(sprintf('Run step `%s`', $this->getStepName($step)));
            $stepsResponseDto = $step->run($stepsResponseDto);
            if (!$stepsResponseDto->getIsSuccessful()) {
                $this->logger->info(
                    sprintf('Step `%s` is failed. Trying to fix it', $this->getStepName($step)),
                    [$stepsResponseDto->getOutputMessage()],
                );
                $stepsResponseDto = $this->runWithFixer($step, $stepsResponseDto);
            }

            if ($stepsResponseDto->isSuccessful() && $stepsResponseDto->getIsStopPropagation()) {
                $this->logger->info(sprintf('Stop propagation from step`%s`', $this->getStepName($step)), [$stepsResponseDto]);

                return $stepsResponseDto;
            }

            if (!$stepsResponseDto->getIsSuccessful()) {
                $this->logger->warning(sprintf('Step `%s` is failed', $this->getStepName($step)), [$stepsResponseDto->getOutputMessage()]);
                $stepsResponseDto->addOutputMessage(sprintf('Step `%s` is failed', $this->getStepName($step)));
                $rollBackExecutionDto = new StepsResponseDto(true);
                foreach (array_reverse($executedSteps) as $executedStep) {
                    if ($executedStep instanceof RollbackStepInterface) {
                        $this->logger->info(sprintf('Run rollback step `%s`', $this->getStepName($executedStep)));
                        $rollBackExecutionDto = $executedStep->rollBack($rollBackExecutionDto);
                    }
                }

                $executedSteps = [];

                if (!$stepsResponseDto->hasErrors()) {
                    return $stepsResponseDto;
                }
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
        $this->logger->info(sprintf('Try to fix step `%s`', $this->getStepName($step)));
        foreach ($this->fixers as $fixer) {
            if (!$fixer->isApplicable($stepsResponseDto)) {
                $this->logger->info(sprintf('Fixer `%s` is not applicable', get_class($fixer)));

                continue;
            }
            $stepsResponseDto->addOutputMessage('Step is failed. It will be reapplied with a fixer');

            $this->logger->info(sprintf('Run fixer `%s`', get_class($fixer)));
            $stepsResponseDto = $fixer->run($stepsResponseDto);
            if (!$stepsResponseDto->getIsSuccessful()) {
                $this->logger->warning(sprintf('Fixer `%s` is failed', get_class($fixer)));

                continue;
            }

            $stepsResponseDto->setError(null);

            $this->logger->info(sprintf('Run step `%s` after fixer', $this->getStepName($step)));
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
