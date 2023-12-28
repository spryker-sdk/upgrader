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
     * @param \Psr\Log\LoggerInterface $logger
     * @param array<\Upgrade\Application\Strategy\StepInterface> $steps
     */
    public function __construct(LoggerInterface $logger, array $steps = [])
    {
        $this->logger = $logger;
        $this->steps = $steps;
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

                return $stepsResponseDto;
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
