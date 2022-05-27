<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Upgrade\Application\Service;

use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\StrategyResolver;

class UpgradeService implements UpgradeServiceInterface
{
    /**
     * @var \Upgrade\Application\Strategy\StrategyResolver
     */
    protected StrategyResolver $strategyResolver;

    /**
     * @var \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     * @param \Upgrade\Application\Strategy\StrategyResolver $strategyResolver
     */
    public function __construct(ConfigurationProviderInterface $configurationProvider, StrategyResolver $strategyResolver)
    {
        $this->configurationProvider = $configurationProvider;
        $this->strategyResolver = $strategyResolver;
    }

    /**
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function upgrade(): StepsResponseDto
    {
        $strategy = $this->strategyResolver->getStrategy($this->configurationProvider->getUpgradeStrategy());

        return $strategy->upgrade();
    }
}
