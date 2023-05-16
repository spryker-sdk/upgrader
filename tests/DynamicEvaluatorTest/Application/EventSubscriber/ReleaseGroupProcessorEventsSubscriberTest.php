<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\EventSubscriber;

use DynamicEvaluator\Application\Checker\CheckerInterface;
use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesSynchronizerInterface;
use DynamicEvaluator\Application\EventSubscriber\ReleaseGroupProcessorEventsSubscriber;
use PHPUnit\Framework\TestCase;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Dto\ViolationDto;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;

class ReleaseGroupProcessorEventsSubscriberTest extends TestCase
{
    /**
     * @return void
     */
    public function testOnPostRequireShouldAddViolationsIntoResponseDto(): void
    {
        // Arrange
        $packagesSynchronizer = $this->createMock(PackagesSynchronizerInterface::class);

        $checkerExecutorEventSubscriber = new ReleaseGroupProcessorEventsSubscriber(
            [
                $this->createCheckerMock(new ViolationDto('violation one')),
                $this->createCheckerMock(new ViolationDto('violation two')),
            ],
            $packagesSynchronizer,
            $this->createConfigurationProviderMock(),
        );

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        // Act
        $checkerExecutorEventSubscriber->onPostRequire($event);

        // Assert
        $violations = $event->getStepsExecutionDto()->getViolations();
        $this->assertCount(2, $violations);
        $this->assertSame('violation one', $violations[0]->getMessage());
        $this->assertSame('violation two', $violations[1]->getMessage());
    }

    /**
     * @return void
     */
    public function testOnPostRequireShouldBeSkippedWhenEvaluatorDisabled(): void
    {
        // Arrange
        $packagesSynchronizer = $this->createMock(PackagesSynchronizerInterface::class);

        $checkerExecutorEventSubscriber = new ReleaseGroupProcessorEventsSubscriber(
            [
                $this->createCheckerMock(new ViolationDto('violation one')),
                $this->createCheckerMock(new ViolationDto('violation two')),
            ],
            $packagesSynchronizer,
            $this->createConfigurationProviderMock(false),
        );

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        // Act
        $checkerExecutorEventSubscriber->onPostRequire($event);

        // Assert
        $violations = $event->getStepsExecutionDto()->getViolations();
        $this->assertEmpty($violations);
    }

    /**
     * @return void
     */
    public function testOnPreProcessorShouldCallSynchronizer(): void
    {
        // Arrange
        $packagesSynchronizer = $this->createMock(PackagesSynchronizerInterface::class);
        $packagesSynchronizer->expects($this->once())->method('clear');

        $checkerExecutorEventSubscriber = new ReleaseGroupProcessorEventsSubscriber(
            [
                $this->createCheckerMock(new ViolationDto('violation two')),
            ],
            $packagesSynchronizer,
            $this->createConfigurationProviderMock(),
        );

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        // Act
        $checkerExecutorEventSubscriber->onPreProcessor($event);
    }

    /**
     * @return void
     */
    public function testOnPreProcessorShouldBeSkippedWhenEvaluatorDisabled(): void
    {
        // Arrange
        $packagesSynchronizer = $this->createMock(PackagesSynchronizerInterface::class);
        $packagesSynchronizer->expects($this->never())->method('clear');

        $checkerExecutorEventSubscriber = new ReleaseGroupProcessorEventsSubscriber(
            [
                $this->createCheckerMock(new ViolationDto('violation two')),
            ],
            $packagesSynchronizer,
            $this->createConfigurationProviderMock(false),
        );

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        // Act
        $checkerExecutorEventSubscriber->onPreProcessor($event);
    }

    /**
     * @return void
     */
    public function testOnPreRequireShouldCallSynchronizer(): void
    {
        // Arrange
        $packagesSynchronizer = $this->createMock(PackagesSynchronizerInterface::class);
        $packagesSynchronizer->expects($this->once())->method('sync');

        $checkerExecutorEventSubscriber = new ReleaseGroupProcessorEventsSubscriber(
            [
                $this->createCheckerMock(new ViolationDto('violation two')),
            ],
            $packagesSynchronizer,
            $this->createConfigurationProviderMock(),
        );

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        // Act
        $checkerExecutorEventSubscriber->onPreRequire($event);
    }

    /**
     * @return void
     */
    public function testOnPreRequireShouldBeSkippedWhenEvaluatorDisabled(): void
    {
        // Arrange
        $packagesSynchronizer = $this->createMock(PackagesSynchronizerInterface::class);
        $packagesSynchronizer->expects($this->never())->method('sync');

        $checkerExecutorEventSubscriber = new ReleaseGroupProcessorEventsSubscriber(
            [
                $this->createCheckerMock(new ViolationDto('violation two')),
            ],
            $packagesSynchronizer,
            $this->createConfigurationProviderMock(false),
        );

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        // Act
        $checkerExecutorEventSubscriber->onPreRequire($event);
    }

    /**
     * @return void
     */
    public function testOnPostProcessorShouldCallSynchronizer(): void
    {
        // Arrange
        $packagesSynchronizer = $this->createMock(PackagesSynchronizerInterface::class);
        $packagesSynchronizer->expects($this->once())->method('clear');

        $checkerExecutorEventSubscriber = new ReleaseGroupProcessorEventsSubscriber(
            [
                $this->createCheckerMock(new ViolationDto('violation two')),
            ],
            $packagesSynchronizer,
            $this->createConfigurationProviderMock(),
        );

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        // Act
        $checkerExecutorEventSubscriber->onPostProcessor($event);
    }

    /**
     * @return void
     */
    public function testOnPostProcessorShouldBeSkippedWhenEvaluatorDisabled(): void
    {
        // Arrange
        $packagesSynchronizer = $this->createMock(PackagesSynchronizerInterface::class);
        $packagesSynchronizer->expects($this->never())->method('clear');

        $checkerExecutorEventSubscriber = new ReleaseGroupProcessorEventsSubscriber(
            [
                $this->createCheckerMock(new ViolationDto('violation two')),
            ],
            $packagesSynchronizer,
            $this->createConfigurationProviderMock(false),
        );

        $event = new ReleaseGroupProcessorEvent(new StepsResponseDto());

        // Act
        $checkerExecutorEventSubscriber->onPostProcessor($event);
    }

    /**
     * @param bool $isEvaluatorEnabled
     *
     * @return \Upgrade\Application\Provider\ConfigurationProviderInterface
     */
    public function createConfigurationProviderMock(bool $isEvaluatorEnabled = true): ConfigurationProviderInterface
    {
        $configurationProvider = $this->createMock(ConfigurationProviderInterface::class);
        $configurationProvider->method('isEvaluatorEnabled')->willReturn($isEvaluatorEnabled);

        return $configurationProvider;
    }

    /**
     * @param \Upgrade\Application\Dto\ViolationDto $violationDto
     *
     * @return \DynamicEvaluator\Application\Checker\CheckerInterface
     */
    public function createCheckerMock(ViolationDto $violationDto): CheckerInterface
    {
        $checker = $this->createMock(CheckerInterface::class);
        $checker->method('check')->willReturn([$violationDto]);

        return $checker;
    }
}
