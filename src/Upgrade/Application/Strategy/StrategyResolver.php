<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Strategy;

use Upgrade\Application\Exception\UpgradeStrategyIsNotDefinedException;

class StrategyResolver
{
    /**
     * @var array<\Upgrade\Application\Strategy\StrategyInterface>
     */
    protected $strategies = [];

    /**
     * @param array<\Upgrade\Application\Strategy\StrategyInterface> $strategies
     */
    public function __construct(array $strategies = [])
    {
        $this->strategies = $strategies;
    }

    /**
     * @param string $strategyName
     *
     * @throws \Upgrade\Application\Exception\UpgradeStrategyIsNotDefinedException
     *
     * @return \Upgrade\Application\Strategy\StrategyInterface
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
