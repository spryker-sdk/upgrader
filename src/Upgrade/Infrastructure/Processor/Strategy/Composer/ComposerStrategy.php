<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy\Composer;

use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Dto\Step\StepsExecutionDto;
use Upgrade\Infrastructure\Processor\Strategy\RollbackStepInterface;
use Upgrade\Infrastructure\Processor\Strategy\StrategyInterface;

class ComposerStrategy implements StrategyInterface
{
    /**
     * @var array<\Upgrade\Infrastructure\Processor\Strategy\StepInterface>
     */
    protected $steps = [];

    /**
     * @param array<\Upgrade\Infrastructure\Processor\Strategy\StepInterface> $steps
     */
    public function __construct(array $steps = [])
    {
        $this->steps = $steps;
    }

    /**
     * @return string
     */
    public function getStrategyName(): string
    {
        return ConfigurationProvider::COMPOSER_STRATEGY;
    }

    /**
     * @return \Upgrade\Infrastructure\Dto\Step\StepsExecutionDto
     */
    public function upgrade(): StepsExecutionDto
    {
        $executedSteps = [];
        $stepsExecutionDto = new StepsExecutionDto(true);

        foreach ($this->steps as $step) {
            $executedSteps[] = $step;

            $stepsExecutionDto = $step->run($stepsExecutionDto);

            if (!$stepsExecutionDto->getIsSuccessful()) {
                $rollBackExecutionDto = new StepsExecutionDto(true);
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
