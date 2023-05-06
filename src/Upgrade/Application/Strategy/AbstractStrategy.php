<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy;

use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Executor\StepExecutorInterface;

abstract class AbstractStrategy implements StrategyInterface
{
    /**
     * @var \Upgrade\Application\Executor\StepExecutorInterface
     */
    protected StepExecutorInterface $stepExecutor;

    /**
     * @param \Upgrade\Application\Executor\StepExecutorInterface $stepExecutor
     */
    public function __construct(StepExecutorInterface $stepExecutor)
    {
        $this->stepExecutor = $stepExecutor;
    }

    /**
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function upgrade(): StepsResponseDto
    {
        $stepsResponseDto = new StepsResponseDto(true);

        return $this->stepExecutor->execute($stepsResponseDto);
    }
}
