<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Service;

use Psr\Log\LoggerInterface;
use SprykerSdk\SdkContracts\Event\MetricEventInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Throwable;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Event\UpgraderEventFactory;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\StrategyResolver;
use Upgrade\Domain\ValueObject\Error;

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
     * @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface
     */
    protected EventDispatcherInterface $eventDispatcher;

    /**
     * @var \Upgrade\Application\Event\UpgraderEventFactory
     */
    protected UpgraderEventFactory $upgraderEventFactory;

    /**
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     * @param \Upgrade\Application\Strategy\StrategyResolver $strategyResolver
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Symfony\Contracts\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Upgrade\Application\Event\UpgraderEventFactory $upgraderEventFactory
     */
    public function __construct(
        ConfigurationProviderInterface $configurationProvider,
        StrategyResolver $strategyResolver,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher,
        UpgraderEventFactory $upgraderEventFactory
    ) {
        $this->configurationProvider = $configurationProvider;
        $this->strategyResolver = $strategyResolver;
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
        $this->upgraderEventFactory = $upgraderEventFactory;
    }

    /**
     * @throws \Throwable
     *
     * @return \Upgrade\Application\Dto\StepsResponseDto
     */
    public function upgrade(): StepsResponseDto
    {
        $startTime = time();

        $this->dispatchUpgraderStartedEvent();

        $stepsResponse = new StepsResponseDto();

        try {
            $strategy = $this->strategyResolver->getStrategy($this->configurationProvider->getUpgradeStrategy());

            $stepsResponse = $strategy->upgrade();
        } catch (Throwable $e) {
            $stepsResponse->setIsSuccessful(false);
            $stepsResponse->setError(Error::createInternalError($e->getMessage()));

            throw $e;
        } finally {
            $this->dispatchUpgraderFinishedEvent($stepsResponse, $startTime);
        }

        return $stepsResponse;
    }

    /**
     * @return void
     */
    protected function dispatchUpgraderStartedEvent(): void
    {
        $upgraderStartedEvent = $this->upgraderEventFactory->createUpgraderStartedEvent();
        $this->eventDispatcher->dispatch($upgraderStartedEvent, MetricEventInterface::class);
    }

    /**
     * @param \Upgrade\Application\Dto\StepsResponseDto $stepsResponse
     * @param int $startTime
     *
     * @return void
     */
    protected function dispatchUpgraderFinishedEvent(StepsResponseDto $stepsResponse, int $startTime): void
    {
        $upgraderFinishedEvent = $this->upgraderEventFactory->createUpgraderFinishedEvent($stepsResponse, time() - $startTime);
        $this->eventDispatcher->dispatch($upgraderFinishedEvent, MetricEventInterface::class);
    }
}
