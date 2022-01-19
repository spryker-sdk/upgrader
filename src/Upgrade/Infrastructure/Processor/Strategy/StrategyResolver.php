<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Processor\Strategy;

use Upgrade\Infrastructure\Exception\UpgradeStrategyIsNotDefinedException;

class StrategyResolver
{
    /**
     * @var array<\Upgrade\Infrastructure\Processor\Strategy\StrategyInterface>
     */
    protected $strategies = [];

    /**
     * @param array<\Upgrade\Infrastructure\Processor\Strategy\StrategyInterface> $strategies
     */
    public function __construct(array $strategies = [])
    {
        $this->strategies = $strategies;
    }

    /**
     * @param string $strategyName
     *
     * @throws \Upgrade\Infrastructure\Exception\UpgradeStrategyIsNotDefinedException
     *
     * @return \Upgrade\Infrastructure\Processor\Strategy\StrategyInterface
     */
    public function getStrategy(string $strategyName): StrategyInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->getStrategyName() === $strategyName) {
                return $strategy;
            }
        }

        throw new UpgradeStrategyIsNotDefinedException();
    }
}
