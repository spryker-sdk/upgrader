<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace DynamicEvaluatorTest\Application\Checker\ClassExtendsUpdatedPackageChecker\EventSubscriber;

use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\ClassExtendsUpdatedPackageChecker;
use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Dto\ViolationDto;
use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\EventSubscriber\ClassExtendsUpdatedPackageCheckerEventSubscriber;
use DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Synchronizer\PackagesSynchronizerInterface;
use PHPUnit\Framework\TestCase;
use Upgrade\Application\Dto\PackageManagerResponseDto;
use Upgrade\Application\Dto\StepsResponseDto;
use Upgrade\Application\Provider\ConfigurationProviderInterface;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorEvent;
use Upgrade\Application\Strategy\ReleaseApp\Processor\Event\ReleaseGroupProcessorPostRequireEvent;

class ClassExtendsUpdatedPackageCheckerEventSubscriberTest extends TestCase
{
    /**
     * @return void
     */
    public function testOnPostRequireShouldAddViolationsIntoResponseDto(): void
    {
        // Arrange
        $packagesSynchronizer = $this->createMock(PackagesSynchronizerInterface::class);

        $checkerExecutorEventSubscriber = new ClassExtendsUpdatedPackageCheckerEventSubscriber(
            $this->createCheckerMock(new ViolationDto('violation one')),
            $packagesSynchronizer,
            $this->createConfigurationProviderMock(),
        );

        $event = new ReleaseGroupProcessorPostRequireEvent(new StepsResponseDto(), new PackageManagerResponseDto(true));

        // Act
        $checkerExecutorEventSubscriber->onPostRequire($event);

        // Assert
        $violations = $event->getStepsExecutionDto()->getViolations();
        $this->assertCount(1, $violations);
        $this->assertSame('violation one', $violations[0]->getMessage());
    }

    /**
     * @return void
     */
    public function testOnPostRequireShouldBeSkippedWhenEvaluatorDisabled(): void
    {
        // Arrange
        $packagesSynchronizer = $this->createMock(PackagesSynchronizerInterface::class);

        $checkerExecutorEventSubscriber = new ClassExtendsUpdatedPackageCheckerEventSubscriber(
            $this->createCheckerMock(new ViolationDto('violation one')),
            $packagesSynchronizer,
            $this->createConfigurationProviderMock(false),
        );

        $event = new ReleaseGroupProcessorPostRequireEvent(new StepsResponseDto(), new PackageManagerResponseDto(true));

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

        $checkerExecutorEventSubscriber = new ClassExtendsUpdatedPackageCheckerEventSubscriber(
            $this->createCheckerMock(new ViolationDto('violation two')),
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

        $checkerExecutorEventSubscriber = new ClassExtendsUpdatedPackageCheckerEventSubscriber(
            $this->createCheckerMock(new ViolationDto('violation two')),
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

        $checkerExecutorEventSubscriber = new ClassExtendsUpdatedPackageCheckerEventSubscriber(
            $this->createCheckerMock(new ViolationDto('violation two')),
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

        $checkerExecutorEventSubscriber = new ClassExtendsUpdatedPackageCheckerEventSubscriber(
            $this->createCheckerMock(new ViolationDto('violation two')),
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

        $checkerExecutorEventSubscriber = new ClassExtendsUpdatedPackageCheckerEventSubscriber(
            $this->createCheckerMock(new ViolationDto('violation two')),
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

        $checkerExecutorEventSubscriber = new ClassExtendsUpdatedPackageCheckerEventSubscriber(
            $this->createCheckerMock(new ViolationDto('violation two')),
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
     * @param \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\Dto\ViolationDto $violationDto
     *
     * @return \DynamicEvaluator\Application\Checker\ClassExtendsUpdatedPackageChecker\ClassExtendsUpdatedPackageChecker
     */
    public function createCheckerMock(ViolationDto $violationDto): ClassExtendsUpdatedPackageChecker
    {
        $checker = $this->createMock(ClassExtendsUpdatedPackageChecker::class);
        $checker->method('check')->willReturn([$violationDto]);

        return $checker;
    }
}
