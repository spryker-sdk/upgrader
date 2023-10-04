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
     * @var \Psr\Log\LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param \Upgrade\Application\Executor\StepExecutorInterface $stepExecutor
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        StepExecutorInterface $stepExecutor,
        LoggerInterface $logger
    ) {
        $this->stepExecutor = $stepExecutor;
        $this->logger = $logger;
    }

    /**
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function upgrade(): StepsResponseDto
    {
        $stepsResponseDto = $this->stepExecutor->execute(new StepsResponseDto(true));

        $this->logger->info('Steps execution is finished', [$stepsResponseDto]);

        return $stepsResponseDto;
    }
}
