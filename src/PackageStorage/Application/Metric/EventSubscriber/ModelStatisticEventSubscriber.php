<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace PackageStorage\Application\Metric\EventSubscriber;

use PackageStorage\Application\Metric\ModuleStatisticUpdaterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorPostRequireEvent;

class ModelStatisticEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var \PackageStorage\Application\Metric\ModuleStatisticUpdaterInterface
     */
    protected ModuleStatisticUpdaterInterface $moduleStatisticFetcher;

    /**
     * @param \PackageStorage\Application\Metric\ModuleStatisticUpdaterInterface $moduleStatisticFetcher
     */
    public function __construct(ModuleStatisticUpdaterInterface $moduleStatisticFetcher)
    {
        $this->moduleStatisticFetcher = $moduleStatisticFetcher;
    }

    /**
     * @return array<mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ReleaseGroupProcessorEvent::PRE_REQUIRE => 'onPreRequire',
            ReleaseGroupProcessorPostRequireEvent::POST_REQUIRE => 'onPostRequire',
        ];
    }

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent $event
     *
     * @return void
     */
    public function onPreRequire(ReleaseGroupProcessorEvent $event): void
    {
        $this->moduleStatisticFetcher->updateStatisticPreRequire($event->getStepsExecutionDto());
    }

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent $event
     *
     * @return void
     */
    public function onPostRequire(ReleaseGroupProcessorEvent $event): void
    {
        $this->moduleStatisticFetcher->updateStatisticPostRequire($event->getStepsExecutionDto());
    }
}
