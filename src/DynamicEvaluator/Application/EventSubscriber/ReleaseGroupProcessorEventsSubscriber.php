<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluator\Application\EventSubscriber;

use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesSynchronizerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;

class ReleaseGroupProcessorEventsSubscriber implements EventSubscriberInterface
{
    /**
     * @var iterable<\DynamicEvaluator\Application\Checker\CheckerInterface>
     */
    protected iterable $checkers;

    /**
     * @var \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesSynchronizerInterface
     */
    protected PackagesSynchronizerInterface $packagesSynchronizer;

    /**
     * @var \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    protected ConfigurationProviderInterface $configurationProvider;

    /**
     * @param iterable<\DynamicEvaluator\Application\Checker\CheckerInterface> $checkers
     * @param \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesSynchronizerInterface $packagesSynchronizer
     * @param \Upgrade\Application\Provider\ConfigurationProviderInterface $configurationProvider
     */
    public function __construct(
        iterable $checkers,
        PackagesSynchronizerInterface $packagesSynchronizer,
        ConfigurationProviderInterface $configurationProvider
    ) {
        $this->checkers = $checkers;
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
            ReleaseGroupProcessorEvent::PRE_REQUIRE => 'onPreRequire',
            ReleaseGroupProcessorEvent::POST_REQUIRE => 'onPostRequire',
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
    public function onPostRequire(ReleaseGroupProcessorEvent $event): void
    {
        if (!$this->configurationProvider->isEvaluatorEnabled()) {
            return;
        }

        $stepsExecutorDto = $event->getStepsExecutionDto();

        foreach ($this->checkers as $checker) {
            $violations = $checker->check();

            foreach ($violations as $violation) {
                $stepsExecutorDto->addViolation($violation);
            }
        }
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
