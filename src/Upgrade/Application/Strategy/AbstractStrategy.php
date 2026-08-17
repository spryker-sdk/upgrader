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
    protected StepExecutorInterface $stepExecutor;

    protected StepExecutorInterface $sendEmptyPrStepExecutor;

    protected LoggerInterface $logger;

    public function __construct(
        StepExecutorInterface $stepExecutor,
        StepExecutorInterface $sendEmptyPrStepExecutor,
        LoggerInterface $logger
    ) {
        $this->stepExecutor = $stepExecutor;
        $this->sendEmptyPrStepExecutor = $sendEmptyPrStepExecutor;
        $this->logger = $logger;
    }

    public function upgrade(): StepsResponseDto
    {
        $stepsResponseDto = $this->stepExecutor->execute(new StepsResponseDto(true));

        $this->sendEmptyPrWithErrors($stepsResponseDto);

        $this->logger->info('Steps execution is finished', [$stepsResponseDto]);

        return $stepsResponseDto;
    }

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

    protected function shouldSendErrorsWithPr(StepsResponseDto $stepsResponseDto): bool
    {
        return !$stepsResponseDto->getIsSuccessful() && $stepsResponseDto->hasErrors() && !$stepsResponseDto->isPullRequestSent();
    }
}
