<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy;

use Psr\Log\LoggerInterface;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Executor\StepExecutorInterface;

abstract class AbstractStrategy implements StrategyInterface
{
    /**
     * @var \Upgrade\Application\Executor\StepExecutorInterface
     */
    protected StepExecutorInterface $stepExecutor;

    /**
     * @var \Upgrade\Application\Executor\StepExecutorInterface
     */
    protected StepExecutorInterface $sendEmptyPrStepExecutor;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param \Upgrade\Application\Executor\StepExecutorInterface $stepExecutor
     * @param \Upgrade\Application\Executor\StepExecutorInterface $sendEmptyPrStepExecutor
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        StepExecutorInterface $stepExecutor,
        StepExecutorInterface $sendEmptyPrStepExecutor,
        LoggerInterface $logger
    ) {
        $this->stepExecutor = $stepExecutor;
        $this->sendEmptyPrStepExecutor = $sendEmptyPrStepExecutor;
        $this->logger = $logger;
    }

    /**
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function upgrade(): StepsResponseDto
    {
        $stepsResponseDto = $this->stepExecutor->execute(new StepsResponseDto(true));

        $this->sendEmptyPrWithErrors($stepsResponseDto);

        $this->logger->info('Steps execution is finished', [$stepsResponseDto]);

        return $stepsResponseDto;
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return void
     */
    protected function sendEmptyPrWithErrors(StepsResponseDto $stepsResponseDto): void
    {
        if (!$this->shouldSendErrorsWithPr($stepsResponseDto)) {
            return;
        }

        $this->logger->info('Send an empty PR with the errors');

        $isSuccessful = $stepsResponseDto->getIsSuccessful();

        $this->sendEmptyPrStepExecutor->execute($stepsResponseDto);

        $stepsResponseDto->setIsSuccessful($isSuccessful);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponseDto
     *
     * @return bool
     */
    protected function shouldSendErrorsWithPr(StepsResponseDto $stepsResponseDto): bool
    {
        return !$stepsResponseDto->getIsSuccessful() && $stepsResponseDto->hasErrors() && !$stepsResponseDto->isPullRequestSent();
    }
}
