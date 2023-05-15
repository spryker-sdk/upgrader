<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace UpgradeTest\Application\Strategy\ReleaseApp\Processor\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Executor\StepExecutorInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;
use Upgrade\Application\Strategy\ReleaseApp\Processor\EventSubscriber\HookEventSubscriber;

class HookEventSubscriberTest extends TestCase
{
    /**
     * @return void
     */
    public function testOnPreRequireShouldCallPreRequireHooks(): void
    {
        // Arrange
        $stepsResponseDto = new StepsResponseDto();

        $preRequireHookExecutorMock = $this->createMock(StepExecutorInterface::class);
        $preRequireHookExecutorMock->expects($this->once())->method('execute')->willReturn($stepsResponseDto);

        $postRequireHookExecutorMock = $this->createMock(StepExecutorInterface::class);
        $postRequireHookExecutorMock->expects($this->never())->method('execute')->willReturn($stepsResponseDto);

        $hookEventSubscriber = new HookEventSubscriber($preRequireHookExecutorMock, $postRequireHookExecutorMock);

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        // Act
        $hookEventSubscriber->onPreRequire($event);

        // Assert
        $this->assertSame($stepsResponseDto, $event->getStepsExecutionDto());
    }

    /**
     * @return void
     */
    public function testOnPostRequireShouldCallPreRequireHooks(): void
    {
        // Arrange
        $stepsResponseDto = new StepsResponseDto();

        $preRequireHookExecutorMock = $this->createMock(StepExecutorInterface::class);
        $preRequireHookExecutorMock->expects($this->never())->method('execute')->willReturn($stepsResponseDto);

        $postRequireHookExecutorMock = $this->createMock(StepExecutorInterface::class);
        $postRequireHookExecutorMock->expects($this->once())->method('execute')->willReturn($stepsResponseDto);

        $hookEventSubscriber = new HookEventSubscriber($preRequireHookExecutorMock, $postRequireHookExecutorMock);

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        // Act
        $hookEventSubscriber->onPostRequire($event);

        // Assert
        $this->assertSame($stepsResponseDto, $event->getStepsExecutionDto());
    }
}
