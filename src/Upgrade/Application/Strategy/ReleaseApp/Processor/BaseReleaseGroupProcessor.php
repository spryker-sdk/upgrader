<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;

abstract class BaseReleaseGroupProcessor implements ReleaseGroupProcessorInterface
{
    /**
     * @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface
     */
    protected EventDispatcherInterface $eventDispatcher;

    /**
     * @param \Symfony\Contracts\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent $event
     * @param string $eventName
     *
     * @return bool
     */
    protected function dispatchEvent(ReleaseGroupProcessorEvent $event, string $eventName): bool
    {
        $this->eventDispatcher->dispatch($event, $eventName);

        return $event->getStepsExecutionDto()->isSuccessful() || $event->getStepsExecutionDto()->getIsStopPropagation();
    }
}
