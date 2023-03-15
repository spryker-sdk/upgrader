<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Service;

use Psr\Log\LoggerInterface;
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
     * @var \Psr\Log\LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     * @param \Upgrade\Application\Strategy\StrategyResolver $strategyResolver
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(ConfigurationProviderInterface $configurationProvider, StrategyResolver $strategyResolver, LoggerInterface $logger)
    {
        $this->configurationProvider = $configurationProvider;
        $this->strategyResolver = $strategyResolver;
        $this->logger = $logger;
    }

    /**
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function upgrade(): StepsResponseDto
    {
        $strategy = $this->strategyResolver->getStrategy($this->configurationProvider->getUpgradeStrategy());

        $stepsResponse =  $strategy->upgrade();

        if (!$stepsResponse->isSuccessful()) {
            $this->logger->error((string) $stepsResponse->getOutputMessage());
        }

        return $stepsResponse;
    }
}
