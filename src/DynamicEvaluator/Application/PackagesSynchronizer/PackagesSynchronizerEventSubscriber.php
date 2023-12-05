<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\PackagesSynchronizer;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;

class PackagesSynchronizerEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var int
     */
    protected const HIGH_PRIORITY_EVENT = 250;

    /**
     * @var \DynamicEvaluator\Application\PackagesSynchronizer\PackagesSynchronizerInterface
     */
    protected PackagesSynchronizerInterface $packagesSynchronizer;

    /**
     * @var \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @param \DynamicEvaluator\Application\PackagesSynchronizer\PackagesSynchronizerInterface $packagesSynchronizer
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     */
    public function __construct(
        PackagesSynchronizerInterface $packagesSynchronizer,
        ConfigurationProviderInterface $configurationProvider
    ) {
        $this->packagesSynchronizer = $packagesSynchronizer;
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @return array<mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ReleaseGroupProcessorEvent::PRE_PROCESSOR => 'onPreProcessor',
            ReleaseGroupProcessorEvent::PRE_REQUIRE => ['onPreRequire', static::HIGH_PRIORITY_EVENT],
            ReleaseGroupProcessorEvent::POST_PROCESSOR => 'onPostProcessor',
        ];
    }

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent $event
     *
     * @return void
     */
    public function onPreProcessor(ReleaseGroupProcessorEvent $event): void
    {
        if (!$this->configurationProvider->isEvaluatorEnabled()) {
            return;
        }

        $this->packagesSynchronizer->clear();
    }

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent $event
     *
     * @return void
     */
    public function onPreRequire(ReleaseGroupProcessorEvent $event): void
    {
        if (!$this->configurationProvider->isEvaluatorEnabled()) {
            return;
        }

        $this->packagesSynchronizer->sync();
    }

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent $event
     *
     * @return void
     */
    public function onPostProcessor(ReleaseGroupProcessorEvent $event): void
    {
        if (!$this->configurationProvider->isEvaluatorEnabled()) {
            return;
        }

        $this->packagesSynchronizer->clear();
    }
}
