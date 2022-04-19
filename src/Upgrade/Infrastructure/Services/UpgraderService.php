<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Infrastructure\Services;

use Upgrade\Application\Dto\Step\StepsExecutionDto;
use Upgrade\Application\Services\UpgraderServiceInterface;
use Upgrade\Infrastructure\Configuration\ConfigurationProvider;
use Upgrade\Infrastructure\Processor\Strategy\StrategyResolver;

class UpgraderService implements UpgraderServiceInterface
{
    /**
     * @var \Upgrade\Infrastructure\Processor\Strategy\StrategyResolver
     */
    protected StrategyResolver $strategyResolver;

    /**
     * @var \Upgrade\Infrastructure\Configuration\ConfigurationProvider
     */
    protected ConfigurationProvider $configurationProvider;

    /**
     * @param \Upgrade\Infrastructure\Configuration\ConfigurationProvider $configurationProvider
     * @param \Upgrade\Infrastructure\Processor\Strategy\StrategyResolver $strategyResolver
     */
    public function __construct(ConfigurationProvider $configurationProvider, StrategyResolver $strategyResolver)
    {
        $this->configurationProvider = $configurationProvider;
        $this->strategyResolver = $strategyResolver;
    }

    /**
     * @return \Upgrade\Application\Dto\Step\StepsExecutionDto
     */
    public function upgrade(): StepsExecutionDto
    {
        $strategy = $this->strategyResolver->getStrategy($this->configurationProvider->getUpgradeStrategy());

        return $strategy->upgrade();
    }
}
