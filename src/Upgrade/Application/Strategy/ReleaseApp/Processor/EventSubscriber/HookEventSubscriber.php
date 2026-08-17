<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Upgrade\Application\Strategy\ReleaseApp\Processor\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Upgrade\Application\Executor\StepExecutorInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorPostRequireEvent;

class HookEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var int
     */
    protected const HIGH_PRIORITY = 100;

    protected StepExecutorInterface $preRequireHookExecutor;

    protected StepExecutorInterface $postRequireHookExecutor;

    public function __construct(
        StepExecutorInterface $preRequireHookExecutor,
        StepExecutorInterface $postRequireHookExecutor
    ) {
        $this->preRequireHookExecutor = $preRequireHookExecutor;
        $this->postRequireHookExecutor = $postRequireHookExecutor;
    }

    /**
     * @return array<mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ReleaseGroupProcessorEvent::PRE_REQUIRE => ['onPreRequire', static::HIGH_PRIORITY],
            ReleaseGroupProcessorPostRequireEvent::POST_REQUIRE => ['onPostRequire', static::HIGH_PRIORITY],
        ];
    }

    public function onPreRequire(ReleaseGroupProcessorEvent $processorEvent): void
    {
        $stepsResponseDto = $this->preRequireHookExecutor->execute($processorEvent->getStepsExecutionDto());
        $processorEvent->setStepsExecutionDto($stepsResponseDto);
    }

    public function onPostRequire(ReleaseGroupProcessorEvent $processorEvent): void
    {
        $stepsResponseDto = $this->postRequireHookExecutor->execute($processorEvent->getStepsExecutionDto());
        $processorEvent->setStepsExecutionDto($stepsResponseDto);
    }
}
