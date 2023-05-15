<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesSynchronizerInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;

class CheckerExecutorEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var iterable<\Upgrade\Application\Checker\CheckerInterface>
     */
    protected iterable $checkers;

    /**
     * @var \Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesSynchronizerInterface
     */
    protected PackagesSynchronizerInterface $packagesSynchronizer;

    /**
     * @param iterable<\Upgrade\Application\Checker\CheckerInterface> $checkers
     * @param \Upgrade\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesSynchronizerInterface $packagesSynchronizer
     */
    public function __construct(
        iterable $checkers,
        PackagesSynchronizerInterface $packagesSynchronizer
    ) {
        $this->checkers = $checkers;
        $this->packagesSynchronizer = $packagesSynchronizer;
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
        $this->packagesSynchronizer->clear();
    }

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent $event
     *
     * @return void
     */
    public function onPreRequire(ReleaseGroupProcessorEvent $event): void
    {
        $this->packagesSynchronizer->sync();
    }

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent $event
     *
     * @return void
     */
    public function onPostRequire(ReleaseGroupProcessorEvent $event): void
    {
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
        $this->packagesSynchronizer->clear();
    }
}
