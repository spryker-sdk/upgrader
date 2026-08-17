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
    protected ModuleStatisticUpdaterInterface $moduleStatisticFetcher;

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

    public function onPreRequire(ReleaseGroupProcessorEvent $event): void
    {
        $this->moduleStatisticFetcher->updateStatisticPreRequire($event->getStepsExecutionDto());
    }

    public function onPostRequire(ReleaseGroupProcessorEvent $event): void
    {
        $this->moduleStatisticFetcher->updateStatisticPostRequire($event->getStepsExecutionDto());
    }
}
