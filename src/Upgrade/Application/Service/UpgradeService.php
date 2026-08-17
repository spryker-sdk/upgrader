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
    protected StrategyResolver $strategyResolver;

    protected ConfigurationProviderInterface $configurationProvider;

    protected LoggerInterface $logger;

    protected EventDispatcherInterface $eventDispatcher;

    protected UpgraderEventFactory $upgraderEventFactory;

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
     */
    public function upgrade(): StepsResponseDto
    {
        $startTime = time();

        $this->logger->info('Starting upgrade process');
        $this->logger->info('BUDDY_RUN_BRANCH: ' . $this->configurationProvider->getBuddyRunBranch());
        $this->dispatchUpgraderStartedEvent();

        $stepsResponse = new StepsResponseDto();

        try {
            $strategy = $this->strategyResolver->getStrategy($this->configurationProvider->getUpgradeStrategy());
            $this->logger->info(sprintf('Using strategy: %s for upgrade', $strategy->getStrategyName()));

            $stepsResponse = $strategy->upgrade();
        } catch (Throwable $e) {
            $stepsResponse->setIsSuccessful(false);
            $stepsResponse->setError(Error::createInternalError($e->getMessage()));

            $this->logger->error($e->getMessage());

            throw $e;
        } finally {
            $this->logger->info('Upgrade process finished');
            $this->dispatchUpgraderFinishedEvent($stepsResponse, $startTime);
        }

        return $stepsResponse;
    }

    protected function dispatchUpgraderStartedEvent(): void
    {
        $upgraderStartedEvent = $this->upgraderEventFactory->createUpgraderStartedEvent();
        $this->eventDispatcher->dispatch($upgraderStartedEvent, MetricEventInterface::class);
    }

    protected function dispatchUpgraderFinishedEvent(StepsResponseDto $stepsResponse, int $startTime): void
    {
        $upgraderFinishedEvent = $this->upgraderEventFactory->createUpgraderFinishedEvent($stepsResponse, time() - $startTime);
        $this->eventDispatcher->dispatch($upgraderFinishedEvent, MetricEventInterface::class);
    }
}
